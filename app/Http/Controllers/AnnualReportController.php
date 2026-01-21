<?php

namespace App\Http\Controllers;

use App\Models\AnnualReport;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class AnnualReportController extends Controller
{
    public function download(AnnualReport $annualReport): Response
    {
        $user = Auth::user();

        if ($annualReport->user_id !== $user->id) {
            abort(403);
        }

        return Storage::disk('public')->download($annualReport->pdf_path);
    }
}
