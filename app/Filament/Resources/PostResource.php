<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CategoryResource\RelationManagers\PostsRelationManager;
use App\Filament\Resources\PostResource\RelationManagers\AuthorsRelationManager;
use App\Filament\Resources\PostResource\Pages;
use App\Filament\Resources\PostResource\RelationManagers;
use App\Filament\Resources\PostResource\RelationManagers\CommentsRelationManager;
use App\Models\Comment;
use App\Models\Post;
use Doctrine\DBAL\Platforms\MySQL\DefaultTableOptions;
use Faker\Core\Color;
use Faker\Core\File;
use Faker\Provider\ar_EG\Text;
use Filament\Forms;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Tabs\Tab;
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
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Str;
use PharIo\Manifest\Author;

class PostResource extends Resource
{
    protected static ?string $model = Post::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([

                Section::make(fn($record) => $record ? ($record->exists ? 'Update Post' : 'View Post') : 'Create a Post')
                    ->description(fn($record) => $record ? ($record->exists ? 'Update the post details' : 'View the post details') : 'Create a new post')
                    ->schema([
                        TextInput::make('title')
                            ->label('Title')
                            ->required()
                            ->placeholder('Post Title')
                            ->live(onBlur: true)
                            ->afterStateUpdated(function (string $operation, string $state, Forms\Set $set) {
                                if ($operation === 'edit') {
                                    return;
                                }
                                $set('slug', Str::slug($state));
                            }),
                        TextInput::make('slug')
                            ->label('Slug')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->placeholder('post-title'),
                        Select::make('category_id')
                            ->label('Category')
                            ->relationship('category', 'name')
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
                            Select::make('published')
                                ->label('Status')
                                ->options([
                                    true => 'Published',
                                    false => 'Draft',
                                ]),
                        ])->columnSpan(1),

                    // Section::make(fn($record) => $record ? ($record->exists ? 'Edit Author Information' : 'View Author Information') : 'Add Author Information')
                    // ->description(fn($record) => $record ? ($record->exists ? 'Edit the author information of your post' : 'View the author information of your post') : 'Add some author information to your post')
                    // ->schema([
                    //     Select::make('authors')
                    //         ->label('Co Authors')
                    //         ->multiple()
                    //         ->relationship('authors', 'name'),
                    // ])->columnSpan(1),
                ]),

            ])->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                ImageColumn::make('thumbnail')
                    ->label('Thumbnail'),
                TextColumn::make('title')
                    ->label('Title')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('slug')
                    ->label('Slug')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                ColorColumn::make('color')
                    ->label('Color')
                    ->toggleable(),
                TextColumn::make('category.name')
                    ->label('Category')
                    ->searchable()
                    ->toggleable()
                    ->sortable(),
                TextColumn::make('tags')
                    ->label('Tags')
                    ->searchable()
                    ->toggleable()
                    ->sortable(),
                CheckboxColumn::make('published')
                    ->label('Published')
                    ->searchable()
                    ->toggleable()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label('Created At')
                    ->date()
                    ->toggleable(),

            ])
            ->filters([
                TernaryFilter::make('published')
                    ->label('Published')
                    ->options([
                        'Published' => true,
                        'Not Published' => false,
                    ]),
                SelectFilter::make('category_id')
                    ->label('Category')
                    ->relationship('category', 'name'),
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
        return [
            AuthorsRelationManager::class,
            CommentsRelationManager::class,
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
