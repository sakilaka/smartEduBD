<?php

namespace App\Providers;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\ServiceProvider;

class QueryBuilderServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // WHERE LIKE QUERY BUILDER
        Builder::macro('whereLike', function ($attribute, $searchTerm) {
            return $this->when($searchTerm, function ($query) use ($attribute, $searchTerm) {
                return $query->where($attribute, 'LIKE', "%{$searchTerm}%");
            });
        });

        // WHERE DATE QUERY BUILDER
        Builder::macro('whereDates', function ($attribute, $fromDate, $toDate) {
            return $this->when($fromDate && $toDate, function ($query) use ($attribute, $fromDate, $toDate) {
                $from = date('Y-m-d', strtotime($fromDate));
                $to   = date('Y-m-d', strtotime($toDate));
                return $query->whereDate($attribute, '>=', $from)->whereDate($attribute, '<=', $to);
            });
        });

        // WHERE ANY EQUAL DATA QUERY BUILDER
        Builder::macro('whereAny', function ($attribute, $searchTerm) {
            return $this->when($searchTerm, function ($query) use ($attribute, $searchTerm) {
                return $query->where($attribute, $searchTerm);
            });
        });

        // SUB-QUERY BUILDER
        Builder::macro('whereSub', function ($subfield, $attribute, $searchTerm) {
            return $this->when($searchTerm, function ($query) use ($attribute, $searchTerm, $subfield) {
                return $query->whereHas($subfield, function ($subquery) use ($searchTerm, $attribute) {
                    $subquery->where($attribute, $searchTerm);
                });
            });
        });

        // SUB-QUERY LIKE BUILDER
        Builder::macro('whereSubLike', function ($subfield, $attribute, $searchTerm) {
            return $this->when($searchTerm, function ($query) use ($attribute, $searchTerm, $subfield) {
                return $query->whereHas($subfield, function ($subquery) use ($searchTerm, $attribute) {
                    $subquery->where($attribute, 'LIKE', "%{$searchTerm}%");
                });
            });
        });
    }
}
