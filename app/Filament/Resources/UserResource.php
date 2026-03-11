<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Hash;

class UserResource extends Resource
{
    protected static ?string $model = User::class;
    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?string $navigationGroup = 'User Management';
    protected static ?int $navigationSort = 1;

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Personal Information')->schema([
                Forms\Components\TextInput::make('name')
                    ->required()->maxLength(255),

                Forms\Components\TextInput::make('email')
                    ->email()->required()->maxLength(255)
                    ->unique(ignoreRecord: true),

                Forms\Components\TextInput::make('phone')
                    ->tel()->maxLength(20),

                Forms\Components\DateTimePicker::make('email_verified_at')
                    ->label('Email Verified At'),
            ])->columns(2),

            Forms\Components\Section::make('Password')->schema([
                Forms\Components\TextInput::make('password')
                    ->password()
                    ->dehydrateStateUsing(fn ($state) => filled($state) ? Hash::make($state) : null)
                    ->dehydrated(fn ($state) => filled($state))
                    ->required(fn (string $context) => $context === 'create')
                    ->label(fn (string $context) => $context === 'create' ? 'Password' : 'New Password (leave blank to keep current)'),
            ]),

            Forms\Components\Section::make('Roles & Status')->schema([
                Forms\Components\Select::make('roles')
                    ->relationship('roles', 'name')
                    ->multiple()
                    ->preload(),

                Forms\Components\Toggle::make('is_active')
                    ->label('Active')
                    ->default(true),
            ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('avatar')
                    ->circular()->defaultImageUrl(fn ($record) => 'https://ui-avatars.com/api/?name=' . urlencode($record->name) . '&background=random'),

                Tables\Columns\TextColumn::make('name')
                    ->searchable()->sortable(),

                Tables\Columns\TextColumn::make('email')
                    ->searchable()->sortable(),

                Tables\Columns\TextColumn::make('roles.name')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'admin'    => 'danger',
                        'vendor'   => 'warning',
                        'customer' => 'success',
                        default    => 'gray',
                    }),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('Active')->boolean(),

                Tables\Columns\IconColumn::make('email_verified_at')
                    ->label('Verified')
                    ->boolean()
                    ->getStateUsing(fn ($record) => ! is_null($record->email_verified_at)),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()->sortable()->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('roles')
                    ->relationship('roles', 'name'),

                Tables\Filters\TernaryFilter::make('is_active')->label('Active'),

                Tables\Filters\TernaryFilter::make('email_verified_at')
                    ->label('Email Verified')
                    ->nullable(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit'   => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
