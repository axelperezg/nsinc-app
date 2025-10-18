# 🧙‍♂️ Wizard de Estrategias - Guía de Implementación

## 📋 Descripción General

Se ha implementado un **Wizard (Asistente paso a paso)** para mejorar significativamente la experiencia del usuario al crear estrategias de comunicación. El wizard divide el proceso largo en **6 pasos lógicos** con barra de progreso visual.

## 🎯 Características Principales

### 1. **Estructura del Wizard (6 Pasos)**

#### Paso 1: Información General
- **Icono:** 📄 (heroicon-o-document-text)
- **Descripción:** Datos básicos de la estrategia
- **Campos:**
  - Año (generado automáticamente)
  - Institución (desde usuario autenticado)
  - Naturaleza Jurídica
  - Responsable
  - Fecha de elaboración
  - Estado de la estrategia
  - Solicitud/Concepto

#### Paso 2: Información Institucional
- **Icono:** 🏢 (heroicon-o-building-office-2)
- **Descripción:** Misión, visión y objetivos
- **Campos:**
  - Misión
  - Visión
  - Objetivo Institucional
  - Objetivo de la Estrategia

#### Paso 3: Plan Nacional de Desarrollo
- **Icono:** 🚩 (heroicon-o-flag)
- **Descripción:** Ejes estratégicos relacionados
- **Campos:**
  - Ejes Generales (4 opciones checkbox)
  - Ejes Transversales (3 opciones checkbox)

#### Paso 4: Presupuesto Anual
- **Icono:** 💰 (heroicon-o-currency-dollar)
- **Descripción:** Define el presupuesto total
- **Campos:**
  - Presupuesto Total Anual (con validaciones y notificaciones)

#### Paso 5: Campañas
- **Icono:** 📣 (heroicon-o-megaphone)
- **Descripción:** Agrega tus campañas de comunicación
- **Campos:**
  - Repeater de Campañas (con sub-formularios complejos)
  - Información General de Campaña
  - Versiones de Campaña
  - Público Objetivo
  - Medios
  - Presupuestos por medio

#### Paso 6: Resumen y Envío
- **Icono:** ✅ (heroicon-o-clipboard-document-check)
- **Descripción:** Revisa y envía tu estrategia
- **Campos:**
  - Resumen Global del Presupuesto
  - Total de Campañas
  - Porcentaje Disponible
  - Presupuesto Disponible
  - Acciones de envío (Enviar a CS, Autorizar DGNC, etc.)

### 2. **Barra de Progreso Visual**

El wizard incluye automáticamente:
- ✅ Barra de progreso en la parte superior
- ✅ Indicadores visuales de pasos completados (iconos de check)
- ✅ Navegación entre pasos (botones Anterior/Siguiente)
- ✅ Indicador del paso actual resaltado
- ✅ Posibilidad de saltar entre pasos completados

### 3. **Características Avanzadas**

#### Persistencia en URL
```php
->persistStepInQueryString()
```
- Los pasos se guardan en la URL (query string)
- Permite compartir enlaces a pasos específicos
- Mantiene el progreso al recargar la página

#### Pasos Saltables
```php
->skippable()
```
- Los usuarios pueden saltar pasos si es necesario
- Útil para revisar información previamente completada
- Facilita la navegación no lineal

#### Iconos de Completado
```php
->completedIcon('heroicon-o-check-circle')
```
- Cada paso muestra un ícono de check cuando está completo
- Feedback visual inmediato del progreso

### 4. **Mejoras en la Vista de Creación**

Se mejoró `create-estrategy.blade.php` con:

#### Banner Informativo
- Diseño atractivo con gradiente azul
- Indica que son 6 pasos
- Información contextual del proceso

#### Indicador de Auto-guardado Mejorado
- Diseño con gradiente verde
- Animación de pulso
- Muestra hora exacta del último guardado

#### Panel de Consejos Útiles
- Lista de consejos para el usuario
- Diseño con gradiente ámbar
- Información sobre navegación y guardado automático

#### Estilos Personalizados
```css
.fi-fo-wizard {
    /* Bordes redondeados y sombras */
}

.fi-fo-wizard-step {
    /* Transiciones suaves */
}

.fi-fo-wizard-step[aria-current="step"] {
    /* Resaltar paso activo con escala */
}
```

## 🎨 Componentes Blade Adicionales

### wizard-progress.blade.php
Componente personalizado para mostrar progreso:
- Barra de progreso animada
- Porcentaje de completado
- Mensajes motivacionales dinámicos
- Soporte para modo oscuro

## 🔧 Implementación Técnica

### Archivo Principal
`app/Filament/Resources/EstrategyResource.php`

### Imports Necesarios
```php
use Filament\Forms\Components\Wizard;
```

### Estructura del Formulario
```php
public static function form(Form $form): Form
{
    return $form
        ->schema([
            Wizard::make([
                Wizard\Step::make('Nombre del Paso')
                    ->description('Descripción breve')
                    ->icon('heroicon-o-icon-name')
                    ->completedIcon('heroicon-o-check-circle')
                    ->schema([
                        // Campos del formulario
                    ]),
                // ... más pasos
            ])
            ->columnSpanFull()
            ->persistStepInQueryString()
            ->skippable()
        ]);
}
```

## 📊 Beneficios de la Implementación

### Para Usuarios
1. ✅ **Menos abrumador**: Información dividida en secciones lógicas
2. ✅ **Mejor orientación**: Saben exactamente en qué paso están
3. ✅ **Progreso visible**: Barra de progreso muestra avance
4. ✅ **Navegación flexible**: Pueden ir y venir entre pasos
5. ✅ **Feedback constante**: Iconos de check para pasos completados

### Para el Sistema
1. ✅ **Mejor organización del código**
2. ✅ **Más fácil de mantener**
3. ✅ **Validación por pasos**
4. ✅ **Mejor UX = menos errores**

## 🚀 Funcionalidades Adicionales

### Auto-guardado
- Se mantiene el auto-guardado cada 30 segundos
- Compatible con el wizard
- Indicador visual mejorado

### Validación
- Las validaciones de campos se mantienen
- Se pueden agregar validaciones por paso
- Notificaciones contextuales

### Responsive
- El wizard es completamente responsive
- Se adapta a dispositivos móviles
- Navegación táctil optimizada

## 📝 Notas de Desarrollo

### Personalización
Para cambiar el comportamiento del wizard:

```php
// Desactivar navegación entre pasos
->skippable(false)

// No persistir en URL
// (comentar o eliminar ->persistStepInQueryString())

// Cambiar iconos
->icon('heroicon-o-custom-icon')
->completedIcon('heroicon-o-custom-check')
```

### Agregar Nuevo Paso
```php
Wizard\Step::make('Nuevo Paso')
    ->description('Descripción del nuevo paso')
    ->icon('heroicon-o-sparkles')
    ->completedIcon('heroicon-o-check-circle')
    ->schema([
        // Campos del nuevo paso
    ]),
```

## 🎯 Próximas Mejoras Sugeridas

1. **Validación por paso**: Implementar validación obligatoria antes de avanzar
2. **Resumen final**: Mostrar resumen completo antes de enviar
3. **Tooltips contextuales**: Ayuda adicional en cada paso
4. **Estimación de tiempo**: Mostrar tiempo estimado por paso
5. **Guardado de progreso**: Indicador visual del porcentaje completado

## 📚 Referencias

- [Filament Wizard Documentation](https://filamentphp.com/docs/3.x/forms/layout/wizard)
- [Heroicons](https://heroicons.com/) - Iconos utilizados
- [Tailwind CSS](https://tailwindcss.com/) - Estilos

---

**Última actualización:** Octubre 2025
**Versión:** 1.0.0


