<?php

namespace App\Providers\Filament;

use App\Filament\Pages\Auth\Login;
use App\Filament\Pages\Dashboard;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\View\PanelsRenderHook;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\HtmlString;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login(Login::class)
            ->brandName('Mulago Specialized Women & Neonatal Hospital')
            ->brandLogo(asset('hospital-logo.svg'))
            ->brandLogoHeight('5.5rem')
            ->favicon(asset('hospital-logo.svg'))
            ->sidebarFullyCollapsibleOnDesktop()
            ->colors(['primary' => Color::Blue])
            ->renderHook(
                PanelsRenderHook::TOPBAR_START,
                fn (): HtmlString => new HtmlString('<span style="font-size:0.75rem;font-weight:600;color:var(--gray-400,#9ca3af);letter-spacing:0.03em;padding-left:0.25rem;">Staff Roster Management System</span>'),
            )
            ->renderHook(
                PanelsRenderHook::STYLES_BEFORE,
                fn (): HtmlString => new HtmlString('<style>.fi-topbar-start { justify-content: flex-start !important; } .fi-topbar .fi-logo { margin-left: 0 !important; }</style>'),
            )
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([Dashboard::class])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                PreventRequestForgery::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }

    public function getLoginRedirectUrl(): string
    {
        $user = auth()->user();

        if (! $user) {
            return route('filament.admin.pages.dashboard');
        }

        return match ($user->role) {
            'in_charge' => route('filament.admin.resources.roster-periods.index'),
            'admin' => route('filament.admin.pages.dashboard'),
            'head_nursing',
            'area_manager' => route('filament.admin.resources.roster-overview.index'),
            default => route('filament.admin.pages.dashboard'),
        };
    }
}
