<?php
namespace App\Http\Middleware;
use Illuminate\Http\Request;
use Closure;
class Rbac
{
	/**
	* Handle an incoming request.
	*
	* @param  \Illuminate\Http\Request  $request
	* @param  \Closure  $next
	* @return mixed
	*/
	public function handle($request, Closure $next)
	{
		$page = $request->segment(1, "home");
		$action = $request->segment(2, "index");
		
		$user = $request->user();
		
		$page_path = strtolower("$page/$action");
		if (!$user->canAccess($page_path)) {
			return abort(403, "Forbidden");
		}
		return $next($request);
	}
}
