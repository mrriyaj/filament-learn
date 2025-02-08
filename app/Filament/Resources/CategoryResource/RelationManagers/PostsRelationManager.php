<?php

namespace App\Filament\Resources\CategoryResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Checkbox;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Columns\CheckboxColumn;

class PostsRelationManager extends RelationManager
{
    protected static string $relationship = 'posts';

    public function form(Form $form): Form
    {
        return $form
            ->schema([Section::make(fn($record) => $record ? ($record->exists ? 'Update Post' : 'View Post') : 'Create a Post')
                ->description(fn($record) => $record ? ($record->exists ? 'Update the post details' : 'View the post details') : 'Create a new post')
                ->schema([
                    TextInput::make('title')
                        ->label('Title')
                        ->required()
                        ->placeholder('Post Title'),
                    TextInput::make('slug')
                        ->label('Slug')
                        ->required()
                        ->unique(ignoreRecord: true)
                        ->placeholder('post-title'),
                    ColorPicker::make('color')
                        ->label('Color')
                        ->required()
                        ->default('#000000'),
                    MarkdownEditor::make('content')
                        ->label('Content')
                        ->required()
                        ->placeholder('Post Content')
                        ->columnSpanFull(),
                ])->columnSpan(2)->columns(2),

            Group::make([
                Section::make(fn($record) => $record ? ($record->exists ? 'Edit Post Thumbnail' : 'View Post Thumbnail') : 'Add Post Thumbnail')
                    ->description(fn($record) => $record ? ($record->exists ? 'Edit the thumbnail image of your post' : 'View the thumbnail image of your post') : 'Add a thumbnail image to your post')
                    ->collapsed()
                    ->schema([
                        FileUpload::make('thumbnail')
                            ->label('Thumbnail')
                            ->disk('public')
                            ->directory('thumbnails'),
                    ])->columnSpan(1),
                Section::make(fn($record) => $record ? ($record->exists ? 'Edit Meta Information' : 'View Meta Information') : 'Add Meta Information')
                    ->description(fn($record) => $record ? ($record->exists ? 'Edit the meta information of your post' : 'View the meta information of your post') : 'Add some meta information to your post')
                    ->schema([
                        TagsInput::make('tags')
                            ->label('Tags')
                            ->placeholder('tag1, tag2, tag3'),
                        Checkbox::make('published')
                            ->required()
                            ->label('Published'),
                    ])->columnSpan(1),

            ]),
            ])->columns(3);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('title')
            ->columns([
                Tables\Columns\TextColumn::make('title'),
                Tables\Columns\TextColumn::make('slug'),
                Tables\Columns\TextColumn::make('tags'),
                CheckboxColumn::make('published'),
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
