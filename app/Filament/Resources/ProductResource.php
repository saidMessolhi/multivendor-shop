<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;
    protected static ?string $navigationIcon = 'heroicon-o-cube';
    protected static ?string $navigationGroup = 'Catalogue';
    protected static ?int $navigationSort = 1;

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Product Information')->schema([
                Forms\Components\TextInput::make('name')
                    ->required()->maxLength(255)->live(onBlur: true)
                    ->afterStateUpdated(fn ($state, callable $set) =>
                        $set('slug', \Illuminate\Support\Str::slug($state))
                    ),

                Forms\Components\TextInput::make('slug')
                    ->required()->maxLength(255)->unique(ignoreRecord: true),

                Forms\Components\Select::make('vendor_id')
                    ->relationship('vendor', 'store_name')
                    ->searchable()->preload()->required(),

                Forms\Components\Select::make('category_id')
                    ->relationship('category', 'name')
                    ->searchable()->preload(),

                Forms\Components\Textarea::make('short_description')
                    ->maxLength(500)->rows(2)->columnSpanFull(),

                Forms\Components\RichEditor::make('description')
                    ->columnSpanFull(),
            ])->columns(2),

            Forms\Components\Section::make('Pricing & Stock')->schema([
                Forms\Components\TextInput::make('price')
                    ->required()->numeric()->prefix('$')->minValue(0),

                Forms\Components\TextInput::make('sale_price')
                    ->numeric()->prefix('$')->minValue(0),

                Forms\Components\TextInput::make('stock')
                    ->required()->integer()->minValue(0)->default(0),

                Forms\Components\TextInput::make('sku')
                    ->maxLength(100)->unique(ignoreRecord: true),

                Forms\Components\Select::make('status')
                    ->options([
                        'active'   => 'Active',
                        'draft'    => 'Draft',
                        'inactive' => 'Inactive',
                    ])
                    ->default('draft')->required(),

                Forms\Components\Toggle::make('is_featured')
                    ->label('Featured Product'),
            ])->columns(3),

            Forms\Components\Section::make('SEO')->schema([
                Forms\Components\TextInput::make('meta_title')->maxLength(255),
                Forms\Components\Textarea::make('meta_description')->maxLength(500)->rows(2),
            ])->columns(2)->collapsed(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()->sortable()->limit(40),

                Tables\Columns\TextColumn::make('vendor.store_name')
                    ->label('Vendor')->searchable()->sortable(),

                Tables\Columns\TextColumn::make('category.name')
                    ->label('Category')->badge(),

                Tables\Columns\TextColumn::make('price')
                    ->formatStateUsing(fn ($state) => '$' . number_format((float)$state, 2))->sortable(),

                Tables\Columns\TextColumn::make('stock')
                    ->sortable()
                    ->color(fn ($state) => $state <= 5 ? 'danger' : 'success'),

                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'success' => 'active',
                        'gray'    => 'draft',
                        'danger'  => 'inactive',
                    ]),

                Tables\Columns\IconColumn::make('is_featured')
                    ->label('Featured')->boolean(),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()->sortable()->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options(['active' => 'Active', 'draft' => 'Draft', 'inactive' => 'Inactive']),

                Tables\Filters\SelectFilter::make('vendor')
                    ->relationship('vendor', 'store_name'),

                Tables\Filters\SelectFilter::make('category')
                    ->relationship('category', 'name'),

                Tables\Filters\TernaryFilter::make('is_featured')->label('Featured'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('activate')
                        ->label('Set Active')
                        ->icon('heroicon-o-check')
                        ->color('success')
                        ->action(fn ($records) => $records->each->update(['status' => 'active'])),

                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit'   => Pages\EditProduct::route('/{record}/edit'),
        ];
    }
}
