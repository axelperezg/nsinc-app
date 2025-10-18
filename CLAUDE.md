# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

This is a Laravel 12 application for managing government communication strategies (NSINC - Sistema de Estrategias de Comunicación). It uses Filament 3.3 for the admin panel and implements a complex role-based access control system with multi-institution support and workflow management.

## Key Commands

### Development

```bash
# Start development server with queue, logs, and vite
composer dev

# Start individual services
php artisan serve
php artisan queue:listen --tries=1
php artisan pail --timeout=0
npm run dev
```

### Database

```bash
# Run migrations
php artisan migrate

# Seed roles and permissions
php artisan db:seed --class=RoleSeeder

# Assign super admin role to user
php artisan user:make-super-admin admin@ejemplo.com
```

### Testing

```bash
# Run all tests
composer test
# or
php artisan test

# Run specific test file
php artisan test tests/Feature/SomeTest.php
```

### Code Quality

```bash
# Format code with Laravel Pint
./vendor/bin/pint

# Format specific file
./vendor/bin/pint app/Models/Estrategy.php
```

### Frontend

```bash
# Build assets for production
npm run build

# Watch for changes during development
npm run dev
```

## Architecture

### Role-Based Access Control

The application implements a sophisticated 5-role system with institution and sector-based filtering:

**Roles:**
- `super_admin` - Full system access, can view/delete all institutions but cannot edit strategies
- `institution_admin` - Manages their own institution's strategies
- `institution_user` - Creates and edits strategies for their institution
- `sector_coordinator` - Reviews and approves strategies from institutions within their sector
- `dgnc_user` - Final authorization authority for strategies across all institutions

**Key Implementation Details:**
- Roles and permissions are configured in `config/roles.php`
- Global scopes in `app/Models/Estrategy.php:188-198` automatically filter queries by user institution
- The `FilterByInstitution` middleware applies institution-level filtering
- Policy-based authorization is defined in `app/Policies/EstrategyPolicy.php`

### Strategy Workflow States

Strategies follow a strict state machine with role-based transitions:

1. **Creada** (Created) - Initial state, editable by institution users
2. **Enviado a CS** (Sent to Sector Coordinator) - Locked for editing
3. **Aceptada CS** (Accepted by CS) - Approved by sector coordinator
4. **Rechazada CS** (Rejected by CS) - Returned to institution for editing
5. **Enviada a DGNC** (Sent to DGNC) - Submitted for final authorization
6. **Autorizada** (Authorized) - Approved by DGNC, enables modifications/cancellations
7. **Rechazada DGNC** (Rejected by DGNC) - Returned to institution
8. **Observada DGNC** (Observed by DGNC) - Requires clarification/correction

**Workflow Actions:**
- Institution users can create "Modificación", "Solventación", or "Cancelación" versions of authorized strategies
- Each new version maintains a reference to the original via `estrategia_original_id`
- Only the latest strategy per institution/year can be modified or transitioned

### Data Model Relationships

**Core Models:**
- `Estrategy` - Main strategy entity with campaigns, versions, and workflow states
- `Campaign` - Advertising campaigns within a strategy (supports multiple per strategy)
- `Version` - Campaign versions with date ranges
- `Institution` - Organizations that create strategies (belongs to Sector, has JuridicalNature)
- `Sector` - Groups multiple institutions (has a sector_coordinator user)
- `Responsable` - Person responsible for an institution's strategy
- `OficioDgncDocument` - Official documents attached to strategies by DGNC users

**Critical Model Features:**
- `Estrategy` model uses mutators (lines 131-183) to auto-sync denormalized name fields from related entities
- `Estrategy::isLatestForInstitutionAndYear()` method determines if a record is the current version
- Budget calculations are performed in real-time in Filament forms across 16 media types

### Filament Resource Structure

The main resource `EstrategyResource.php` contains:
- Complex nested repeaters for campaigns with 16 budget fields
- Conditional field visibility based on user role and strategy state
- Real-time budget calculations and warnings
- Custom actions for workflow transitions (enviar_cs, autorizar_dgnc, rechazar_dgnc, etc.)
- Table actions filtered by role and strategy state

**Custom Pages:**
- `ModificarEstrategy` - Creates a "Modificación" copy of an authorized strategy
- `SolventarEstrategy` - Creates a "Solventación" copy to address DGNC observations
- `CancelarEstrategy` - Creates a "Cancelación" request for an authorized strategy

### Institution Filtering Mechanism

**How it works:**
1. Global scope in `Estrategy::booted()` applies `where('institution_id', $user->institution_id)` for non-admin users
2. Filament resources use `modifyQueryUsing()` to apply additional sector-based filtering for coordinators
3. Super admin and DGNC users bypass all filters to see all institutions
4. Form fields auto-populate institution data from authenticated user's institution

### Budget Management

Strategies contain a total `presupuesto` (annual budget). Each campaign distributes this across 16 media categories:
- Televisoras (Commercial TV)
- Radiodifusoras (Commercial Radio)
- Medios Digitales (Digital/Community Radio)
- Diarios CDMX, Estados, Extranjeros (Newspapers)
- Revistas (Magazines)
- Cine (Cinema)
- Medios Complementarios (Complementary Media)
- Pre/Post Estudios (Research)
- Diseño, Producción, Pre/Post-Producción, Copiado (Production)

The form provides:
- Per-campaign totals
- Global campaign total
- Budget utilization percentage
- Remaining budget warnings

## Important Conventions

### Working with Estrategies

- Always check `isLatestForInstitutionAndYear()` before allowing modifications
- Use `estrategia_original_id` to track modification chains
- The `concepto` field determines the type: "Registro", "Modificación", "Solventación", "Cancelación"
- Denormalized fields (`institution_name`, `juridical_nature_name`, `responsable_name`) are auto-synced via mutators

### Filament Forms

- Use `->dehydrated()` on disabled fields to include them in form submission
- Hidden fields carry the actual IDs while disabled TextInputs show names to users
- The helper method `createDecimalField()` in `EstrategyResource` handles budget fields with 6-decimal precision

### Year Filtering

- Strategies are filtered by year using table filters (defaults to current year)
- The year is passed via `request()->get('tableFilters.anio.anio', now()->year)` in form defaults
- Each institution can have one active strategy per year (with multiple versions via modifications)

### Role-Based UI Visibility

- Use `Auth::user()->role->name` to check role
- Use `Auth::user()->isSuperAdmin()`, `isSectorCoordinator()`, `isDgncUser()` helper methods
- Filament actions use `->visible(function ($record) { ... })` for role-based display
- Navigation items use `shouldRegisterNavigation()` to hide/show menu items

## File Locations

- **Models:** `app/Models/`
- **Filament Resources:** `app/Filament/Resources/`
- **Policies:** `app/Policies/`
- **Migrations:** `database/migrations/`
- **Seeders:** `database/seeders/`
- **Config:** `config/roles.php` for role/permission definitions
- **Commands:** `app/Console/Commands/AssignSuperAdminRole.php`
- **Helpers:** `app/Helpers/PermissionHelper.php`

## Common Development Tasks

### Adding a New Role

1. Add role definition to `config/roles.php`
2. Update `database/seeders/RoleSeeder.php`
3. Run `php artisan db:seed --class=RoleSeeder`
4. Update `EstrategyResource` visibility conditions if needed
5. Add helper methods to `User` model if appropriate

### Modifying Strategy Workflow

1. Update state options in `Estrategy::getEstadosOptions()`
2. Modify table action visibility conditions in `EstrategyResource::table()`
3. Update policy methods in `EstrategyPolicy.php`
4. Test state transitions for each role

### Adding Campaign Budget Fields

1. Add database column via migration
2. Update `Campaign` model's `$fillable`
3. Add field to form schema in `EstrategyResource::form()` using `createDecimalField()`
4. Update sum calculations in "Resumen de Medios" and "Resumen Global" placeholders
5. Ensure field uses `step(0.000001)` and `round(..., 6)` for precision

## Testing Notes

- Tests use SQLite in-memory database (`:memory:`)
- `phpunit.xml` configures test environment variables
- Factories are located in `database/factories/`
- Use `php artisan test` (not `phpunit` directly) to ensure Laravel environment is loaded
