<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PostResource\Pages;
use App\Filament\Resources\PostResource\RelationManagers;
use App\Models\Post;
use Faker\Core\Color;
use Faker\Core\File;
use Filament\Forms;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Form;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Support\Markdown;
use Filament\Tables;
use Filament\Tables\Columns\CheckboxColumn;
use Filament\Tables\Columns\ColorColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PostResource extends Resource
{
    protected static ?string $model = Post::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([

                Section::make('Create a Post')
                    ->description('Create a new post')
                    ->schema([
                        TextInput::make('title')
                            ->label('Title')
                            ->required()
                            ->placeholder('Post Title'),
                        TextInput::make('slug')
                            ->label('Slug')
                            ->required()
                            ->placeholder('post-title'),
                        Select::make('category_id')
                            ->label('Category')
                            ->options(
                                fn() => \App\Models\Category::pluck('name', 'id')
                            )
                            ->required(),
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
                    Section::make('Post Image')
                        ->description('Add Image to your post')
                        ->collapsed()
                        ->schema([
                            FileUpload::make('thumbnail')
                                ->label('Thumbnail')
                                ->disk('public')
                                ->directory('thumbnails'),
                        ])->columnSpan(1),
                    Section::make('Meta')
                        ->description('Add some meta information to your post')
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

    public static function table(Table $table): Table
    {
        return $table
            ->columns([

                ImageColumn::make('thumbnail')
                    ->label('Thumbnail')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('title')
                    ->label('Title')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('slug')
                    ->label('Slug')
                    ->searchable()
                    ->sortable(),
                ColorColumn::make('color')
                    ->label('Color')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('category.name')
                    ->label('Category')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('tags')
                    ->label('Tags')
                    ->searchable()
                    ->sortable(),
                CheckboxColumn::make('published')
                    ->label('Published')
                    ->searchable()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
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
            'index' => Pages\ListPosts::route('/'),
            'create' => Pages\CreatePost::route('/create'),
            'edit' => Pages\EditPost::route('/{record}/edit'),
        ];
    }
}
