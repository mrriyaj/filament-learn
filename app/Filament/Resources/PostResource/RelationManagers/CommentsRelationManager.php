<?php

namespace App\Filament\Resources\PostResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Forms\Components\Select;
use Filament\Tables;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CommentsRelationManager extends RelationManager
{
    protected static string $relationship = 'comments';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
            Select::make('user_id')
                ->label('User')
                ->relationship('user', 'name')
                ->required()
                ->searchable()
                ->preload(),
            Textarea::make('content')
                ->label('Content')
                ->required()
                ->placeholder('Comment Content'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('comment')
            ->columns([Tables\Columns\TextColumn::make('user.name')
                ->label('User')
                ->searchable()
                ->sortable(),
            Tables\Columns\TextColumn::make('user.email')
                ->label('Email')
                ->searchable()
                ->sortable(),
            Tables\Columns\TextColumn::make('content')
                ->label('Content')
                ->searchable()
                ->sortable(),
            Tables\Columns\TextColumn::make('created_at')
                ->label('Created At')
                ->searchable()
                ->sortable(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
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
}
