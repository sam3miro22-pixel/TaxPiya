package com.taxpiya.app;

import android.net.Uri;
import android.os.Build;
import android.os.Bundle;
import android.webkit.CookieManager;
import android.webkit.GeolocationPermissions;
import android.webkit.WebResourceRequest;
import android.webkit.WebSettings;
import android.webkit.WebView;

import androidx.annotation.Nullable;
import androidx.browser.customtabs.CustomTabsIntent;

import com.getcapacitor.Bridge;
import com.getcapacitor.BridgeActivity;
import com.getcapacitor.BridgeWebChromeClient;
import com.getcapacitor.BridgeWebViewClient;

public class MainActivity extends BridgeActivity {

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
        ws.setUserAgentString(ws.getUserAgentString() + " Taxpiya/Android");

        CookieManager cm = CookieManager.getInstance();
        cm.setAcceptCookie(true);
        if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.LOLLIPOP) {
            cm.setAcceptThirdPartyCookies(webView, true);
        }

        webView.setWebChromeClient(new BridgeWebChromeClient(bridge) {
            @Override
            public void onGeolocationPermissionsShowPrompt(
                    String origin, GeolocationPermissions.Callback callback) {
                callback.invoke(origin, true, false);
            }
        });

        webView.setWebViewClient(new BridgeWebViewClient(bridge) {
            private boolean isAllowed(Uri uri) {
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
                return host.endsWith("taxpiya.com")
                        || host.endsWith("onrender.com")
                        || host.endsWith("google.com")
                        || host.contains(".google.")
                        || host.endsWith("googleapis.com")
                        || host.endsWith("gstatic.com")
                        || host.endsWith("firebaseapp.com")
                        || host.endsWith("googleusercontent.com");
            }

            @Override
            public boolean shouldOverrideUrlLoading(WebView view, WebResourceRequest request) {
                if (!request.isForMainFrame()) {
                    return super.shouldOverrideUrlLoading(view, request);
                }
                Uri uri = request.getUrl();
                if (isAllowed(uri)) {
                    return false;
                }
                openCustomTab(uri.toString());
                return true;
            }
        });
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
