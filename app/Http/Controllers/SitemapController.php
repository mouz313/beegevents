<?php

namespace App\Http\Controllers;

use App\Models\Hall;
use App\Models\ServiceListing;
use App\Models\ServiceCategory;

class SitemapController extends Controller
{
    public function index()
    {
        $halls = Hall::with('vendorProfile')->get();
        $listings = ServiceListing::with('vendorProfile', 'serviceCategory')->get();
        $categories = ServiceCategory::all();

        $content = '<?xml version="1.0" encoding="UTF-8"?>';
        $content .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';

        $content .= '<url><loc>' . url('/') . '</loc><priority>1.0</priority><changefreq>weekly</changefreq></url>';
        $content .= '<url><loc>' . route('browse.index') . '</loc><priority>0.9</priority><changefreq>daily</changefreq></url>';

        foreach ($categories as $cat) {
            $content .= '<url><loc>' . route('browse.category', $cat->slug) . '</loc><priority>0.7</priority><changefreq>weekly</changefreq></url>';
        }

        foreach ($halls as $hall) {
            $content .= '<url><loc>' . route('browse.hall', $hall) . '</loc><priority>0.6</priority><changefreq>monthly</changefreq></url>';
        }

        foreach ($listings as $listing) {
            $content .= '<url><loc>' . route('browse.listing', $listing) . '</loc><priority>0.5</priority><changefreq>monthly</changefreq></url>';
        }

        $content .= '<url><loc>' . route('corporate.leads.create') . '</loc><priority>0.3</priority><changefreq>monthly</changefreq></url>';

        $content .= '</urlset>';

        return response($content, 200, ['Content-Type' => 'application/xml']);
    }
}
