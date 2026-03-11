<?php

namespace App\Providers\Filament;

use App\Filament\Resources\CategoryResource;
use App\Filament\Resources\OrderResource;
use App\Filament\Resources\ProductResource;
use App\Filament\Resources\UserResource;
use App\Filament\Resources\VendorResource;
use App\Filament\Widgets\LatestOrdersWidget;
use App\Filament\Widgets\StatsOverviewWidget;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\NavigationGroup;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->colors(['primary' => Color::Orange])
            ->brandName('Multivendor Admin')
            ->navigationGroups([
                NavigationGroup::make('Sales'),
                NavigationGroup::make('Catalogue'),
                NavigationGroup::make('User Management'),
                NavigationGroup::make('Settings'),
            ])
            ->resources([
                OrderResource::class,
                ProductResource::class,
                CategoryResource::class,
                UserResource::class,
                VendorResource::class,
            ])
            ->pages([
                Pages\Dashboard::class,
            ])
            ->widgets([
                StatsOverviewWidget::class,
                LatestOrdersWidget::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ])
            ->authGuard('web')
            ->spa();
    }
}
