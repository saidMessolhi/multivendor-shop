<?php

namespace App\Filament\Resources;

use App\Filament\Resources\VendorResource\Pages;
use App\Models\Vendor;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class VendorResource extends Resource
{
    protected static ?string $model = Vendor::class;
    protected static ?string $navigationIcon = 'heroicon-o-building-storefront';
    protected static ?string $navigationGroup = 'User Management';
    protected static ?int $navigationSort = 2;

    public static function getNavigationBadge(): ?string
    {
        $pending = static::getModel()::where('status', 'pending')->count();
        return $pending > 0 ? (string) $pending : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Store Information')->schema([
                Forms\Components\TextInput::make('store_name')
                    ->required()->maxLength(255),

                Forms\Components\TextInput::make('store_slug')
                    ->required()->maxLength(255)
                    ->unique(ignoreRecord: true),

                Forms\Components\Textarea::make('description')
                    ->maxLength(2000)->rows(4),

                Forms\Components\TextInput::make('phone')->tel(),
                Forms\Components\TextInput::make('city'),
                Forms\Components\TextInput::make('country'),
            ])->columns(2),

            Forms\Components\Section::make('Status & Commission')->schema([
                Forms\Components\Select::make('status')
                    ->options([
                        'pending'   => 'Pending',
                        'approved'  => 'Approved',
                        'suspended' => 'Suspended',
                        'rejected'  => 'Rejected',
                    ])
                    ->required(),

                Forms\Components\TextInput::make('commission_rate')
                    ->numeric()->suffix('%')
                    ->minValue(0)->maxValue(100)
                    ->placeholder(config('shop.commission_rate')),

                Forms\Components\TextInput::make('paypal_email')->email(),
                Forms\Components\TextInput::make('stripe_account_id'),
            ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('logo')
                    ->circular()
                    ->defaultImageUrl(fn ($record) => 'https://ui-avatars.com/api/?name=' . urlencode($record->store_name) . '&background=random'),

                Tables\Columns\TextColumn::make('store_name')
                    ->searchable()->sortable(),

                Tables\Columns\TextColumn::make('user.name')
                    ->label('Owner')->searchable(),

                Tables\Columns\TextColumn::make('user.email')
                    ->label('Email')->searchable()->toggleable(),

                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'warning' => 'pending',
                        'success' => 'approved',
                        'danger'  => ['suspended', 'rejected'],
                    ]),

                Tables\Columns\TextColumn::make('balance')
                    ->formatStateUsing(fn ($state) => '$' . number_format((float)$state, 2))->sortable(),

                Tables\Columns\TextColumn::make('products_count')
                    ->label('Products')
                    ->counts('products')
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()->sortable()->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pending'   => 'Pending',
                        'approved'  => 'Approved',
                        'suspended' => 'Suspended',
                        'rejected'  => 'Rejected',
                    ]),
            ])
            ->actions([
                Tables\Actions\Action::make('approve')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn (Vendor $record) => $record->status === 'pending')
                    ->requiresConfirmation()
                    ->action(function (Vendor $record) {
                        $record->update(['status' => 'approved', 'approved_at' => now()]);
                        Notification::make()->title('Vendor approved!')->success()->send();
                    }),

                Tables\Actions\Action::make('suspend')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->visible(fn (Vendor $record) => $record->status === 'approved')
                    ->requiresConfirmation()
                    ->action(function (Vendor $record) {
                        $record->update(['status' => 'suspended']);
                        Notification::make()->title('Vendor suspended.')->warning()->send();
                    }),

                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('approve_selected')
                        ->label('Approve Selected')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->requiresConfirmation()
                        ->action(fn ($records) => $records->each->update(['status' => 'approved', 'approved_at' => now()])),

                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListVendors::route('/'),
            'create' => Pages\CreateVendor::route('/create'),
            'edit'   => Pages\EditVendor::route('/{record}/edit'),
        ];
    }
}
