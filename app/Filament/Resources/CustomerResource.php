<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CustomerResource\Pages;
use App\Filament\Resources\CustomerResource\RelationManagers;
use App\Models\Customer;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Actions\Action;


class CustomerResource extends Resource
{
    protected static ?string $model = Customer::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('email')
                    ->email()
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('points')
                    ->required()
                    ->numeric()
                    ->default(0),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable(),
                Tables\Columns\TextColumn::make('points')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('add point')
                ->form([
                    Forms\Components\TextInput::make('points')
                        ->label('Points to Add')
                        ->required()
                        ->numeric()
                        ->minValue(1),  // Ensure that the user can only add positive points
                ])
                ->action(function ($record, $data) {
                    // Increment the customer's points by the input value
                    
                    $record->update(['points' => $record->points + $data['points']]);
                    
                    
                    // Insert a record into the loyalty_points table
                    DB::table('loyalty_points')->insert([
                        'customer_id' => $record->id,
                        'points' => $data['points'],
                        'type' => 'add',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }),

                Tables\Actions\Action::make('redeem point')
                ->form([
                    Forms\Components\TextInput::make('points')
                        ->label('Points to Redeem')
                        ->required()
                        ->numeric()
                        ->minValue(1),
                ])
                ->action(function ($record, $data) {
                    if ($record->points >= $data['points']) {
                        // // Decrement the customer's points by the input value
                        $record->update(['points' => $record->points - $data['points']]);

                        
                        
                        // Insert a record into the loyalty_points table
                        DB::table('loyalty_points')->insert([
                            'customer_id' => $record->id,
                            'points' => -$data['points'],  // Negative points for redemption
                            'type' => 'redeem',
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                        

                    } else {
                        throw new \Exception('Insufficient points to redeem.');
                    }
                })
                
                
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
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
