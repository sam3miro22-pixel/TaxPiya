package com.taxpiya.app;

import android.Manifest;
import android.content.pm.PackageManager;
import android.net.Uri;
import android.os.Build;
import android.os.Bundle;
import android.webkit.CookieManager;
import android.webkit.GeolocationPermissions;
import android.webkit.WebResourceRequest;
import android.webkit.WebSettings;
import android.webkit.WebView;

import androidx.annotation.NonNull;
import androidx.annotation.Nullable;
import androidx.browser.customtabs.CustomTabsIntent;
import androidx.core.app.ActivityCompat;
import androidx.core.content.ContextCompat;

import com.getcapacitor.Bridge;
import com.getcapacitor.BridgeActivity;
import com.getcapacitor.BridgeWebChromeClient;
import com.getcapacitor.BridgeWebViewClient;

public class MainActivity extends BridgeActivity {

    private static final int REQ_LOCATION = 9001;
    private GeolocationPermissions.Callback pendingGeoCallback;
    private String pendingGeoOrigin;

    @Override
    protected void onCreate(@Nullable Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);

        Bridge bridge = getBridge();
        WebView webView = bridge.getWebView();
        if (webView == null) {
            return;
        }

        WebSettings ws = webView.getSettings();
        ws.setDomStorageEnabled(true);
        ws.setDatabaseEnabled(true);
        ws.setGeolocationEnabled(true);
        ws.setMixedContentMode(WebSettings.MIXED_CONTENT_COMPATIBILITY_MODE);

        String baseUA = ws.getUserAgentString();
        if (baseUA != null && baseUA.contains("; wv)")) {
            ws.setUserAgentString(baseUA.replace("; wv)", ")") + " Taxpiya/Android");
        } else {
            ws.setUserAgentString(baseUA + " Taxpiya/Android");
        }

        CookieManager cm = CookieManager.getInstance();
        cm.setAcceptCookie(true);
        if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.LOLLIPOP) {
            cm.setAcceptThirdPartyCookies(webView, true);
        }

        webView.setWebChromeClient(new BridgeWebChromeClient(bridge) {
            @Override
            public void onGeolocationPermissionsShowPrompt(
                    String origin, GeolocationPermissions.Callback callback) {
                if (ContextCompat.checkSelfPermission(MainActivity.this, Manifest.permission.ACCESS_FINE_LOCATION)
                        == PackageManager.PERMISSION_GRANTED) {
                    callback.invoke(origin, true, false);
                    return;
                }
                pendingGeoCallback = callback;
                pendingGeoOrigin = origin;
                ActivityCompat.requestPermissions(
                        MainActivity.this,
                        new String[]{
                                Manifest.permission.ACCESS_FINE_LOCATION,
                                Manifest.permission.ACCESS_COARSE_LOCATION
                        },
                        REQ_LOCATION
                );
            }

            @Override
            public void onPermissionRequest(android.webkit.PermissionRequest request) {
                request.grant(request.getResources());
            }
        });


        webView.setWebViewClient(new BridgeWebViewClient(bridge) {
            private boolean isGoogleOAuth(Uri uri) {
                if (uri == null) {
                    return false;
                }
                String host = uri.getHost() == null ? "" : uri.getHost().toLowerCase();
                String path = uri.getPath() == null ? "" : uri.getPath().toLowerCase();
                if (host.contains("accounts.google.com")) {
                    return true;
                }
                if (host.contains("google.com") && (path.contains("oauth") || path.contains("signin"))) {
                    return true;
                }
                return host.endsWith("firebaseapp.com") && path.contains("__/auth/handler");
            }

            private boolean isAppHost(Uri uri) {
                if (uri == null) {
                    return false;
                }
                String scheme = uri.getScheme() == null ? "" : uri.getScheme().toLowerCase();
                if ("about".equals(scheme) || "data".equals(scheme)) {
                    return true;
                }
                String host = uri.getHost() == null ? "" : uri.getHost().toLowerCase();
                if ("localhost".equals(host) || "127.0.0.1".equals(host)) {
                    return true;
                }
                if (!"https".equals(scheme) && !"http".equals(scheme)) {
                    return false;
                }
                return host.endsWith("taxpiya.com") || host.endsWith("onrender.com");
            }

            @Override
            public boolean shouldOverrideUrlLoading(WebView view, WebResourceRequest request) {
                if (!request.isForMainFrame()) {
                    return super.shouldOverrideUrlLoading(view, request);
                }
                Uri uri = request.getUrl();
                if (isGoogleOAuth(uri)) {
                    openCustomTab(uri.toString());
                    return true;
                }
                if (isAppHost(uri)) {
                    return false;
                }
                openCustomTab(uri.toString());
                return true;
            }
        });
    }

    @Override
    public void onRequestPermissionsResult(int requestCode, @NonNull String[] permissions, @NonNull int[] grantResults) {
        super.onRequestPermissionsResult(requestCode, permissions, grantResults);
        if (requestCode == REQ_LOCATION && pendingGeoCallback != null) {
            boolean granted = grantResults.length > 0
                    && grantResults[0] == PackageManager.PERMISSION_GRANTED;
            pendingGeoCallback.invoke(pendingGeoOrigin, granted, false);
            pendingGeoCallback = null;
            pendingGeoOrigin = null;
        }
    }

    private void openCustomTab(String url) {
        try {
            CustomTabsIntent cti = new CustomTabsIntent.Builder().build();
            cti.launchUrl(this, Uri.parse(url));
        } catch (Throwable t) {
            try {
                startActivity(new android.content.Intent(
                        android.content.Intent.ACTION_VIEW, Uri.parse(url)));
            } catch (Throwable ignored) {
            }
        }
    }
}
