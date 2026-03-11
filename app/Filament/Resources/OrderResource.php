<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrderResource\Pages;
use App\Models\Order;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;
    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';
    protected static ?string $navigationGroup = 'Sales';
    protected static ?int $navigationSort = 1;

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('status', 'pending')->count() ?: null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make()->schema([
                Forms\Components\Select::make('status')
                    ->options([
                        'pending'    => 'Pending',
                        'processing' => 'Processing',
                        'shipped'    => 'Shipped',
                        'delivered'  => 'Delivered',
                        'cancelled'  => 'Cancelled',
                        'refunded'   => 'Refunded',
                    ])->required(),

                Forms\Components\Select::make('payment_status')
                    ->options([
                        'pending' => 'Pending',
                        'paid'    => 'Paid',
                        'failed'  => 'Failed',
                        'refunded'=> 'Refunded',
                    ])->required(),
            ])->columns(2),
        ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema([
            Infolists\Components\Section::make('Order Details')->schema([
                Infolists\Components\TextEntry::make('order_number')->fontFamily('mono'),
                Infolists\Components\TextEntry::make('user.name')->label('Customer'),
                Infolists\Components\TextEntry::make('user.email')->label('Email'),
                Infolists\Components\TextEntry::make('created_at')->dateTime(),
            ])->columns(2),

            Infolists\Components\Section::make('Payment')->schema([
                Infolists\Components\TextEntry::make('payment_method')->badge(),
                Infolists\Components\TextEntry::make('payment_status')->badge()
                    ->color(fn ($state) => match($state) {
                        'paid' => 'success', 'failed' => 'danger', default => 'warning'
                    }),
                Infolists\Components\TextEntry::make('subtotal')->formatStateUsing(fn ($state) => '$' . number_format((float)$state, 2)),
                Infolists\Components\TextEntry::make('shipping_amount')->label('Shipping')->formatStateUsing(fn ($state) => '$' . number_format((float)$state, 2)),
                Infolists\Components\TextEntry::make('tax_amount')->label('Tax')->formatStateUsing(fn ($state) => '$' . number_format((float)$state, 2)),
                Infolists\Components\TextEntry::make('discount_amount')->label('Discount')->formatStateUsing(fn ($state) => '$' . number_format((float)$state, 2)),
                Infolists\Components\TextEntry::make('total_amount')->label('Total')->formatStateUsing(fn ($state) => '$' . number_format((float)$state, 2))->weight('bold'),
                Infolists\Components\TextEntry::make('commission_amount')->label('Commission')->formatStateUsing(fn ($state) => '$' . number_format((float)$state, 2)),
            ])->columns(4),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('order_number')
                    ->fontFamily('mono')->searchable()->sortable(),

                Tables\Columns\TextColumn::make('user.name')
                    ->label('Customer')->searchable(),

                Tables\Columns\TextColumn::make('total_amount')
                    ->formatStateUsing(fn ($state) => '$' . number_format((float)$state, 2))->sortable(),

                Tables\Columns\TextColumn::make('commission_amount')
                    ->label('Commission')->formatStateUsing(fn ($state) => '$' . number_format((float)$state, 2))->sortable(),

                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'warning' => 'pending',
                        'primary' => 'processing',
                        'info'    => 'shipped',
                        'success' => 'delivered',
                        'danger'  => ['cancelled', 'refunded'],
                    ]),

                Tables\Columns\BadgeColumn::make('payment_status')
                    ->label('Payment')
                    ->colors([
                        'warning' => 'pending',
                        'success' => 'paid',
                        'danger'  => 'failed',
                    ]),

                Tables\Columns\TextColumn::make('payment_method')
                    ->label('Method')->badge()->toggleable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending', 'processing' => 'Processing',
                        'shipped' => 'Shipped', 'delivered' => 'Delivered',
                        'cancelled' => 'Cancelled',
                    ]),

                Tables\Filters\SelectFilter::make('payment_status')
                    ->options(['pending' => 'Pending', 'paid' => 'Paid', 'failed' => 'Failed']),

                Tables\Filters\Filter::make('created_at')
                    ->form([
                        Forms\Components\DatePicker::make('from'),
                        Forms\Components\DatePicker::make('until'),
                    ])
                    ->query(function ($query, array $data) {
                        return $query
                            ->when($data['from'],  fn ($q) => $q->whereDate('created_at', '>=', $data['from']))
                            ->when($data['until'], fn ($q) => $q->whereDate('created_at', '<=', $data['until']));
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('mark_paid')
                        ->label('Mark as Paid')
                        ->icon('heroicon-o-banknotes')
                        ->color('success')
                        ->requiresConfirmation()
                        ->action(fn ($records) => $records->each->update(['payment_status' => 'paid'])),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListOrders::route('/'),
            'view'   => Pages\ViewOrder::route('/{record}'),
            'edit'   => Pages\EditOrder::route('/{record}/edit'),
        ];
    }
}
