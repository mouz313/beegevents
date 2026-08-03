<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureVendorPackageAccess
{
    public function handle(Request $request, Closure $next): Response
    {
        $profile = $request->user()?->vendorProfile;

        if (! $profile) {
            return $next($request);
        }

        if ($profile->status === 'blocked' || $profile->status === 'suspended') {
            $allowed = [
                'vendor.dashboard',
                'vendor.packages.index',
                'vendor.packages.checkout',
                'vendor.packages.intent',
                'vendor.packages.confirm',
                'vendor.packages.manual',
                'vendor.profile.create',
                'vendor.profile.store',
                'vendor.profile.update',
            ];

            if (! in_array($request->route()?->getName(), $allowed, true)) {
                if ($profile->status === 'blocked') {
                    return redirect()->route('vendor.packages.index')
                        ->with('warning', 'Your package has expired. Buy a package to show your listings on the website.');
                }

                return redirect()->route('vendor.dashboard')
                    ->with('warning', 'Your account is suspended. Contact support.');
            }
        }

        return $next($request);
    }
}
