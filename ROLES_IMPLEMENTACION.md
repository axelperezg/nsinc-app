# Implementación de Sistema de Roles y Control de Acceso por Institución

## Descripción

Este sistema implementa un control de acceso granular basado en roles y instituciones, permitiendo que cada usuario solo vea la información de su institución, excepto los super administradores que pueden ver todo.

## Características

- **Control de Acceso por Institución**: Los usuarios solo ven datos de su institución
- **Rol Super Admin**: Acceso completo a todas las instituciones
- **Roles Granulares**: Diferentes niveles de permisos según el rol
- **Filtrado Automático**: Los datos se filtran automáticamente según el usuario
- **Políticas de Autorización**: Control de acceso a nivel de modelo

## Roles Disponibles

### 1. Super Administrador (`super_admin`)
- **Permisos**: Acceso completo a todas las instituciones
- **Funcionalidades**: Ver, crear, editar y eliminar estrategias de cualquier institución
- **Uso**: Administradores del sistema

### 2. Administrador de Institución (`institution_admin`)
- **Permisos**: Gestionar solo su institución
- **Funcionalidades**: Ver, crear, editar y eliminar estrategias de su institución
- **Uso**: Administradores de instituciones específicas

### 3. Usuario de Institución (`institution_user`)
- **Permisos**: Ver solo su institución
- **Funcionalidades**: Ver estrategias de su institución
- **Uso**: Usuarios básicos de instituciones

## Instalación y Configuración

### 1. Ejecutar Migraciones

```bash
php artisan migrate
```

### 2. Ejecutar Seeder de Roles

```bash
php artisan db:seed --class=RoleSeeder
```

### 3. Asignar Rol de Super Admin

```bash
php artisan user:make-super-admin admin@ejemplo.com
```

## Uso del Sistema

### Para Usuarios Normales
- Solo ven estrategias de su institución
- No pueden cambiar la institución en formularios
- Filtros automáticos aplicados

### Para Super Admin
- Ven todas las estrategias de todas las instituciones
- Pueden cambiar la institución en formularios
- Acceso completo a todas las funcionalidades
- Filtros de institución disponibles

## Archivos Creados/Modificados

### Modelos
- `app/Models/Role.php` - Modelo de roles
- `app/Models/Permission.php` - Modelo de permisos
- `app/Models/User.php` - Modificado para incluir roles
- `app/Models/Estrategy.php` - Modificado con scope global

### Middleware
- `app/Http/Middleware/FilterByInstitution.php` - Filtrado por institución

### Políticas
- `app/Policies/EstrategyPolicy.php` - Autorización para estrategias

### Recursos de Filament
- `app/Filament/Resources/EstrategyResource.php` - Modificado para filtrado
- `app/Filament/Resources/UserResource.php` - Modificado para incluir roles

### Widgets
- `app/Filament/Widgets/EstrategyOverview.php` - Estadísticas filtradas

### Seeders
- `database/seeders/RoleSeeder.php` - Datos iniciales de roles

### Comandos
- `app/Console/Commands/AssignSuperAdminRole.php` - Asignar super admin

### Configuración
- `config/roles.php` - Configuración de roles y permisos
- `app/Providers/AuthServiceProvider.php` - Registro de políticas

### Helpers
- `app/Helpers/PermissionHelper.php` - Funciones de verificación

## Migraciones

- `database/migrations/xxxx_xx_xx_create_roles_and_permissions_tables.php`

## Funcionalidades Implementadas

### 1. Filtrado Automático
- Scope global en el modelo Estrategy
- Filtrado automático por institución del usuario
- Excepción para super admin

### 2. Control de Acceso en Filament
- Tablas filtradas automáticamente
- Formularios con campos condicionales
- Filtros visibles solo para super admin

### 3. Políticas de Autorización
- Verificación de permisos en operaciones CRUD
- Control granular por institución
- Validación automática de acceso

### 4. Dashboard Personalizado
- Widget con estadísticas filtradas
- Información contextual según el rol
- Métricas específicas por institución

## Seguridad

- **Filtrado a nivel de base de datos**: Previene acceso no autorizado
- **Políticas de autorización**: Control de acceso a nivel de aplicación
- **Validación de formularios**: Verificación de permisos en creación/edición
- **Middleware de filtrado**: Aplicación consistente de restricciones

## Mantenimiento

### Agregar Nuevos Roles
1. Agregar el rol en `config/roles.php`
2. Actualizar el seeder `RoleSeeder.php`
3. Ejecutar `php artisan db:seed --class=RoleSeeder`

### Agregar Nuevos Permisos
1. Agregar el permiso en `config/roles.php`
2. Actualizar el seeder `RoleSeeder.php`
3. Ejecutar `php artisan db:seed --class=RoleSeeder`

### Modificar Permisos de Roles
1. Editar `config/roles.php`
2. Actualizar el seeder `RoleSeeder.php`
3. Ejecutar `php artisan db:seed --class=RoleSeeder`

## Troubleshooting

### Usuario no ve datos
- Verificar que tenga un rol asignado
- Verificar que tenga una institución asignada
- Verificar que el rol tenga los permisos correctos

### Super admin no puede ver todo
- Verificar que el rol sea `super_admin`
- Verificar que el usuario tenga el rol asignado
- Verificar que el scope global esté funcionando

### Errores de permisos
- Verificar que las políticas estén registradas
- Verificar que el AuthServiceProvider esté configurado
- Verificar que los roles y permisos existan en la base de datos

## Próximos Pasos

1. **Implementar cache por institución** para mejorar performance
2. **Agregar logs de auditoría** para rastrear accesos
3. **Implementar roles dinámicos** desde la interfaz de administración
4. **Agregar notificaciones** para cambios de estado
5. **Implementar reportes** filtrados por institución

## Soporte

Para soporte técnico o preguntas sobre la implementación, contactar al equipo de desarrollo.
