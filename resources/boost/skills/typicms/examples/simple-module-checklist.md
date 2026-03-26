# Simple Module Creation Checklist

Use this checklist when creating a new TypiCMS module.

## Files to Create

### 1. Model (`Modules/ModuleName/Models/ModelName.php`)

- [ ] Extend `Illuminate\Database\Eloquent\Model`
- [ ] Add `HasTranslations` trait
- [ ] Add core traits: `HasAdminUrls`, `HasBodyPresenter`, `HasContentPresenter`, `HasConfigurableOrder`, `HasFiles`, `HasOgImage`, `HasSelectableFields`, `HasSlugScope`, `Historable`, `Navigable`, `Publishable`
- [ ] Define `$translatable` array
- [ ] Define `$tipTapContent` array if using rich text
- [ ] Add `url()` and `previewUrl()` methods for public URLs
- [ ] Add `thumb()` attribute
- [ ] Add relationships (image, ogImage, etc.)

### 2. Migration (`Modules/ModuleName/database/migrations/create_modulename_table.php.stub`)

- [ ] Use JSON columns for translatable fields
- [ ] Add `new Expression('(JSON_OBJECT())')` default for JSON fields
- [ ] Add foreign keys for images (`files` table)
- [ ] Only create `up()` method (no `down()`)

### 3. Service Provider (`Modules/ModuleName/Providers/ModuleServiceProvider.php`)

- [ ] Merge config
- [ ] Load routes
- [ ] Load views from `__DIR__ . '/../../resources/views/'`
- [ ] Publish migrations, views, and resources
- [ ] Attach `SlugObserver`
- [ ] Attach `TipTapHTMLObserver` if using rich text
- [ ] Register sidebar view composer
- [ ] Register page view composer for public views

### 4. Config (`Modules/ModuleName/config/modulename.php`)

- [ ] Set `linkable_to_page`
- [ ] Set `per_page`
- [ ] Define `order` array
- [ ] Configure `sidebar` (icon, weight)
- [ ] Define `permissions`

### 5. Controllers

- [ ] `AdminController` - index, create, edit, store, update
- [ ] `ApiController` - index, updatePartial, destroy
- [ ] `PublicController` - index, show (if public-facing)

### 6. Form Request (`Modules/ModuleName/Http/Requests/FormRequest.php`)

- [ ] Extend `AbstractFormRequest`
- [ ] Use `.*` suffix for translatable fields
- [ ] Add validation rules

### 7. Routes (`Modules/ModuleName/routes/modulename.php`)

- [ ] Public routes (if linkable to page)
- [ ] Admin routes with permissions
- [ ] API routes

### 8. Sidebar Composer (`Modules/ModuleName/Composers/SidebarViewComposer.php`)

- [ ] Check gate permission
- [ ] Add to the appropriate sidebar group

### 9. Views (`resources/views/vendor/modulename/`)

- [ ] `admin/index.blade.php`
- [ ] `admin/create.blade.php`
- [ ] `admin/edit.blade.php`
- [ ] `admin/_form.blade.php`
- [ ] `public/index.blade.php` (if public)
- [ ] `public/show.blade.php` (if public)

## Post-Creation Steps

1. [ ] Add path repository to root `composer.json`
2. [ ] Run `composer require typicms/modulename`
3. [ ] Register service provider in `bootstrap/providers.php` (before `AppServiceProvider`)
4. [ ] Run migration: `php artisan migrate`
5. [ ] Clear cache: `php artisan cache:clear`
6. [ ] Add permissions to roles in admin panel
7. [ ] Link module to a page (if `linkable_to_page` is true)

## Naming Conventions

| Type          | Convention                     | Example                          |
|---------------|--------------------------------|----------------------------------|
| Module folder | PascalCase plural              | `Modules/Events`                 |
| Model         | PascalCase singular            | `Pertner`, `Event`               |
| Table         | snake_case plural              | `partners`, `events`             |
| Route name    | kebab-case plural and singular | `index-partners`, `edit-event`   |
| Config key    | snake_case plural              | `typicms.modules.events`         |
| Permission    | snake_case plural              | `read partners`, `create events` |
