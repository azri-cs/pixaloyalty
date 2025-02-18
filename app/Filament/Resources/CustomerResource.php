<?php

namespace App\Filament\Resources;

use App\Enums\PointTransactionType;
use App\Filament\Resources\CustomerResource\Pages;
use App\Filament\Resources\CustomerResource\RelationManagers\PointTransactionsRelationManager;
use App\Models\Customer;
use Filament\Forms;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CustomerResource extends Resource
{
    protected static ?string $model = Customer::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('business_id')
                    ->relationship('business', 'name')
                    ->required(),
                Forms\Components\TextInput::make('first_name')
                    ->maxLength(255),
                Forms\Components\TextInput::make('last_name')
                    ->maxLength(255),
                Forms\Components\TextInput::make('email')
                    ->email()
                    ->maxLength(255),
                Forms\Components\TextInput::make('phone_number')
                    ->rules(['nullable', 'regex:/^\+?[0-9\-\(\)\s]{7,20}$/'])
                    ->maxLength(255),
                Forms\Components\TextInput::make('point_balance')
                    ->numeric()
                    ->default(0)
                    ->label('Points'),
                Forms\Components\Textarea::make('description')
                    ->maxLength(65535)
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('business.name')
                    ->label('Business')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('first_name')
                    ->label('Name')
                    ->formatStateUsing(function ($record) {
                        return $record->first_name . ' ' . $record->last_name;
                    })
                    ->searchable(['first_name', 'last_name']),
                Tables\Columns\TextColumn::make('email')
                    ->searchable(),
                Tables\Columns\TextColumn::make('phone_number')
                    ->searchable(),
                Tables\Columns\TextColumn::make('point_balance')
                    ->label('Points')
                    ->formatStateUsing(fn ($state) => number_format($state))
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('deleted_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->hidden(fn($record) => $record->trashed()),
                Tables\Actions\Action::make('issue_points')
                    ->hidden(fn($record) => $record->trashed())
                    ->icon('heroicon-o-plus-circle')
                    ->color('success')
                    ->form([
                        TextInput::make('amount')
                            ->label('Points to Issue')
                            ->numeric()
                            ->required()
                            ->minValue(1),
                        Textarea::make('description')
                            ->required()
                            ->maxLength(255),
                    ])
                    ->action(function (Customer $record, array $data): void {
                        $record->pointTransactions()->create([
                            'transaction_type' => PointTransactionType::ISSUE->value,
                            'amount' => $data['amount'],
                            'description' => $data['description'],
                        ]);

                        $record->increment('point_balance', $data['amount']);

                        Notification::make()
                            ->success()
                            ->title('Points Issued')
                            ->body("{$data['amount']} points have been issued.")
                            ->send();
                    }),

                Tables\Actions\Action::make('deduct_points')
                    ->hidden(fn($record) => $record->trashed())
                    ->icon('heroicon-o-minus-circle')
                    ->color('danger')
                    ->form([
                        TextInput::make('amount')
                            ->label('Points to Deduct')
                            ->numeric()
                            ->required()
                            ->minValue(1),
                        Textarea::make('description')
                            ->required()
                            ->maxLength(255),
                    ])
                    ->action(function (Customer $record, array $data): void {
                        if ($record->point_balance < $data['amount']) {
                            Notification::make()
                                ->danger()
                                ->title('Insufficient Points')
                                ->body("Customer only has {$record->point_balance} points available.")
                                ->send();
                            return;
                        }

                        $record->pointTransactions()->create([
                            'transaction_type' => PointTransactionType::DEDUCT->value,
                            'amount' => $data['amount'],
                            'description' => $data['description'],
                        ]);

                        $record->decrement('point_balance', $data['amount']);

                        Notification::make()
                            ->success()
                            ->title('Points Deducted')
                            ->body("{$data['amount']} points have been deducted.")
                            ->send();
                    }),
                Tables\Actions\RestoreAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->recordUrl(function ($record) {
                if ($record->trashed()) {
                    return null;
                }

                return Pages\EditCustomer::getUrl([$record->id]);
            })
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make()
                    ->hidden(function (Pages\ListCustomers $livewire) {
                        return $livewire->activeTab === 'archived';
                    }),
                Tables\Actions\RestoreBulkAction::make()
                    ->hidden(function (Pages\ListCustomers $livewire) {
                        return $livewire->activeTab !== 'archived';
                    }),
            ]);
    }

    public static function getRelationManagers(): array
    {
        return [
            PointTransactionsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCustomers::route('/'),
            'create' => Pages\CreateCustomer::route('/create'),
            'edit' => Pages\EditCustomer::route('/{record}/edit'),
        ];
    }
}
