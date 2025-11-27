# Guía de Pruebas para Usuarios Finales - Sistema NSINC

## Índice
1. [Introducción](#introducción)
2. [Roles del Sistema](#roles-del-sistema)
3. [Estados de la Estrategia](#estados-de-la-estrategia)
4. [Conceptos de Estrategia](#conceptos-de-estrategia)
5. [Flujos de Prueba](#flujos-de-prueba)
   - [5.1 Flujo de Registro (Flujo Normal)](#51-flujo-de-registro-flujo-normal)
   - [5.2 Flujo de Rechazo por Coordinadora de Sector](#52-flujo-de-rechazo-por-coordinadora-de-sector)
   - [5.3 Flujo de Rechazo por DGNC](#53-flujo-de-rechazo-por-dgnc)
   - [5.4 Flujo de Observación por DGNC](#54-flujo-de-observación-por-dgnc)
   - [5.5 Flujo de Modificación](#55-flujo-de-modificación)
   - [5.6 Flujo de Cancelación](#56-flujo-de-cancelación)
6. [Validaciones Importantes](#validaciones-importantes)
7. [Casos de Prueba Específicos](#casos-de-prueba-específicos)

---

## Introducción

Esta guía describe los casos de prueba que deben realizarse para validar el correcto funcionamiento del Sistema de Estrategias de Comunicación (NSINC). El sistema maneja dos partidas presupuestales:

- **Partida 36101**: Comunicación Social
- **Partida 36201**: Promoción y Publicidad

Cada partida tiene flujos independientes pero con el mismo comportamiento.

---

## Roles del Sistema

El sistema cuenta con los siguientes roles:

| Rol | Descripción | Permisos Principales |
|-----|-------------|---------------------|
| **institution_user** | Usuario de Institución | Crear, editar y enviar estrategias de su institución |
| **sector_coordinator** | Coordinadora de Sector | Evaluar estrategias de las instituciones de su sector |
| **dgnc_user** | Usuario DGNC | Autorizar, rechazar u observar estrategias de todas las instituciones |
| **super_admin** | Super Administrador | Ver todas las estrategias, editar campos críticos, eliminar registros |

---

## Estados de la Estrategia

| Estado | Descripción | Quién lo establece |
|--------|-------------|-------------------|
| **Creada** | Estado inicial, editable por la institución | Sistema (al crear) / Institución |
| **Enviado a CS** | Enviada a Coordinadora de Sector para evaluación | Usuario de Institución |
| **Aceptada CS** | Aprobada por Coordinadora de Sector | Coordinadora de Sector |
| **Rechazada CS** | Rechazada por Coordinadora de Sector, vuelve a "Creada" | Coordinadora de Sector |
| **Enviada a DGNC** | Enviada a DGNC para autorización | Coordinadora de Sector |
| **Autorizada** | Autorizada por DGNC, habilita modificaciones/cancelaciones | Usuario DGNC |
| **Rechazada DGNC** | Rechazada por DGNC, vuelve a "Creada" | Usuario DGNC |
| **Observada DGNC** | Observada por DGNC, requiere solventación | Usuario DGNC |

---

## Conceptos de Estrategia

| Concepto | Descripción | Cuándo se usa |
|----------|-------------|---------------|
| **Registro** | Primera versión de la estrategia anual | Al crear una nueva estrategia |
| **Modificación** | Cambios a una estrategia autorizada | Cuando se necesita modificar una estrategia ya autorizada |
| **Solventación** | Respuesta a observaciones de DGNC | Cuando DGNC marca una estrategia como "Observada" |
| **Cancelación** | Solicitud de cancelación de estrategia | Cuando se desea cancelar una estrategia autorizada |

---

## Flujos de Prueba

### 5.1 Flujo de Registro (Flujo Normal)

**Objetivo**: Validar el flujo completo desde la creación hasta la autorización de una estrategia.

**Diagrama de flujo**:
```
Creada → Enviado a CS → Aceptada CS → Enviada a DGNC → Autorizada
```

#### **Paso 1: Creación de Estrategia (institution_user)**

**Usuario**: Usuario de Institución

**Acciones a probar**:

1. Iniciar sesión como usuario de institución
2. Navegar a "Comunicación Social" o "Promoción y Publicidad"
3. Hacer clic en "Crear Estrategia"
4. Completar el formulario paso a paso:
   - **Paso 1 - Datos Generales**: Año, presupuesto anual, responsable
   - **Paso 2 - Objetivos**: Misión, visión, objetivos institucionales y de estrategia
   - **Paso 3 - Específicos**:
     - Para Comunicación Social: Seleccionar ejes del Plan Nacional
     - Para Promoción y Publicidad: Entorno de mercado y metas generales
   - **Paso 4 - Campañas**: Crear al menos una campaña con:
     - Información general (nombre, tipo, audiencia, periodo)
     - Versiones (al menos una versión con fechas y medios)
     - Distribución de presupuesto en los 16 rubros de medios
5. Verificar que el estado inicial sea "Creada"
6. Guardar la estrategia

**Validaciones**:
- ✓ El presupuesto total de campañas debe sumar exactamente el presupuesto anual
- ✓ Si las Radios Comunitarias (Medios Digitales) son < 1%, se muestra advertencia
- ✓ Los campos de institución, naturaleza jurídica y responsable se llenan automáticamente
- ✓ El estado inicial es "Creada"
- ✓ El concepto es "Registro"

---

#### **Paso 2: Envío a Coordinadora de Sector (institution_user)**

**Usuario**: Usuario de Institución

**Acciones a probar**:

1. En la lista de estrategias, localizar la estrategia creada
2. Verificar que el botón "Enviar a CS" esté visible
3. Hacer clic en "Enviar a CS"
4. Confirmar el modal de confirmación
5. Verificar el cambio de estado

**Validaciones**:
- ✓ El botón "Enviar a CS" solo aparece si el estado es "Creada", "Rechazada CS" o "Rechazada DGNC"
- ✓ Al enviar, se valida que la suma de campañas sea igual al presupuesto anual
- ✓ Si la validación falla, se muestra error y no cambia el estado
- ✓ Si la validación pasa, el estado cambia a "Enviado a CS"
- ✓ Después de enviar, el botón "Editar" desaparece (la estrategia ya no es editable)
- ✓ Se muestra notificación de éxito

---

#### **Paso 3: Evaluación por Coordinadora de Sector (sector_coordinator)**

**Usuario**: Coordinadora de Sector

**Acciones a probar**:

1. Iniciar sesión como coordinadora de sector
2. Navegar a la lista de estrategias
3. Verificar que se vean solo las estrategias de instituciones de su sector
4. Localizar la estrategia en estado "Enviado a CS"
5. Hacer clic en el botón "Ver" para revisar la estrategia
6. Hacer clic en el botón "Evaluar Estrategia"
7. En el modal, seleccionar "Aceptada CS"
8. Confirmar

**Validaciones**:
- ✓ La coordinadora solo ve estrategias de su sector
- ✓ El botón "Evaluar Estrategia" solo aparece si el estado es "Enviado a CS"
- ✓ El modal permite seleccionar entre "Aceptada CS" y "Rechazada CS"
- ✓ Al seleccionar "Aceptada CS", el estado cambia correctamente
- ✓ Se muestra notificación de éxito
- ✓ La coordinadora NO puede editar la estrategia

---

#### **Paso 4: Envío a DGNC (sector_coordinator)**

**Usuario**: Coordinadora de Sector

**Acciones a probar**:

1. Localizar la estrategia en estado "Aceptada CS"
2. Verificar que el botón "Enviar a DGNC" esté visible
3. Hacer clic en "Enviar a DGNC"
4. Confirmar el modal
5. Verificar el cambio de estado

**Validaciones**:
- ✓ El botón "Enviar a DGNC" solo aparece si el estado es "Aceptada CS"
- ✓ Al enviar, el estado cambia a "Enviada a DGNC"
- ✓ Se registra la fecha de envío a DGNC (campo `fecha_envio_dgnc`)
- ✓ Se muestra notificación de éxito

---

#### **Paso 5: Autorización por DGNC (dgnc_user)**

**Usuario**: Usuario DGNC

**Acciones a probar**:

1. Iniciar sesión como usuario DGNC
2. Navegar a la lista de estrategias
3. Verificar que se vean todas las estrategias de todas las instituciones
4. Localizar la estrategia en estado "Enviada a DGNC"
5. Revisar la estrategia (botón "Ver")
6. Hacer clic en el botón "Autorizar DGNC"
7. Confirmar en el modal
8. Verificar el cambio de estado

**Validaciones**:
- ✓ El usuario DGNC ve todas las estrategias de todas las instituciones
- ✓ El botón "Autorizar DGNC" solo aparece si el estado es "Enviada a DGNC"
- ✓ Al autorizar, el estado cambia a "Autorizada"
- ✓ Se muestra notificación de éxito
- ✓ Una vez autorizada, aparecen los botones "Modificar Estrategia" y "Cancelar Estrategia" para usuarios de institución

---

### 5.2 Flujo de Rechazo por Coordinadora de Sector

**Objetivo**: Validar que una estrategia rechazada por CS vuelve a ser editable por la institución.

**Diagrama de flujo**:
```
Enviado a CS → Rechazada CS → (vuelve a Creada) → Editar → Enviar a CS
```

#### **Paso 1: Rechazo por Coordinadora de Sector**

**Usuario**: Coordinadora de Sector

**Acciones a probar**:

1. Localizar una estrategia en estado "Enviado a CS"
2. Hacer clic en "Evaluar Estrategia"
3. Seleccionar "Rechazada CS"
4. Confirmar

**Validaciones**:
- ✓ El estado cambia a "Rechazada CS"
- ✓ Se muestra notificación de advertencia
- ✓ La estrategia vuelve a ser editable para la institución

---

#### **Paso 2: Corrección por Institución**

**Usuario**: Usuario de Institución

**Acciones a probar**:

1. Localizar la estrategia en estado "Rechazada CS"
2. Verificar que el botón "Editar" esté visible
3. Hacer clic en "Editar"
4. Realizar los cambios necesarios
5. Guardar los cambios
6. Enviar nuevamente a CS

**Validaciones**:
- ✓ El botón "Editar" aparece para estrategias en estado "Rechazada CS"
- ✓ El botón "Enviar a CS" aparece para estrategias en estado "Rechazada CS"
- ✓ Se puede editar y volver a enviar sin problemas

---

### 5.3 Flujo de Rechazo por DGNC

**Objetivo**: Validar que una estrategia rechazada por DGNC vuelve a ser editable por la institución.

**Diagrama de flujo**:
```
Enviada a DGNC → Rechazada DGNC → (vuelve a Creada) → Editar → Enviar a CS
```

#### **Paso 1: Rechazo por DGNC**

**Usuario**: Usuario DGNC

**Acciones a probar**:

1. Localizar una estrategia en estado "Enviada a DGNC"
2. Hacer clic en el botón "Rechazar DGNC"
3. Confirmar en el modal

**Validaciones**:
- ✓ El botón "Rechazar DGNC" solo aparece si el estado es "Enviada a DGNC"
- ✓ El estado cambia a "Rechazada DGNC"
- ✓ Se muestra notificación de advertencia
- ✓ La estrategia vuelve a ser editable para la institución

---

#### **Paso 2: Corrección por Institución**

**Usuario**: Usuario de Institución

**Acciones a probar**:

1. Localizar la estrategia en estado "Rechazada DGNC"
2. Verificar que los botones "Editar" y "Enviar a CS" estén visibles
3. Editar la estrategia y realizar correcciones
4. Guardar cambios
5. Enviar nuevamente a CS

**Validaciones**:
- ✓ El botón "Editar" aparece para estrategias en estado "Rechazada DGNC"
- ✓ El botón "Enviar a CS" aparece para estrategias en estado "Rechazada DGNC"
- ✓ Se puede editar y volver a enviar sin problemas

---

### 5.4 Flujo de Observación por DGNC

**Objetivo**: Validar que una estrategia observada por DGNC permite crear una solventación.

**Diagrama de flujo**:
```
Enviada a DGNC → Observada DGNC → Solventar → (nueva estrategia con concepto "Solventación")
```

#### **Paso 1: Observación por DGNC**

**Usuario**: Usuario DGNC

**Acciones a probar**:

1. Localizar una estrategia en estado "Enviada a DGNC"
2. Hacer clic en el botón "Observar DGNC"
3. Confirmar en el modal

**Validaciones**:
- ✓ El botón "Observar DGNC" solo aparece si el estado es "Enviada a DGNC"
- ✓ El estado cambia a "Observada DGNC"
- ✓ Se muestra notificación de advertencia
- ✓ Para la institución, aparece el botón "Solventar Estrategia"

---

#### **Paso 2: Solventación por Institución**

**Usuario**: Usuario de Institución

**Acciones a probar**:

1. Localizar la estrategia en estado "Observada DGNC"
2. Verificar que el botón "Solventar Estrategia" esté visible
3. Hacer clic en "Solventar Estrategia"
4. Confirmar el modal
5. Se crea una nueva estrategia (duplicado) con:
   - Concepto: "Solventación"
   - Estado: "Creada"
   - Referencia a la estrategia original (`estrategia_original_id`)
6. Editar la nueva estrategia para realizar las correcciones
7. Enviar a CS

**Validaciones**:
- ✓ El botón "Solventar Estrategia" solo aparece si el estado es "Observada DGNC"
- ✓ Se crea una nueva estrategia con concepto "Solventación"
- ✓ La nueva estrategia mantiene referencia a la original
- ✓ La nueva estrategia está en estado "Creada" y es editable
- ✓ La nueva estrategia contiene todos los datos de la original (campañas incluidas)
- ✓ Se puede editar y enviar a través del flujo normal

---

### 5.5 Flujo de Modificación

**Objetivo**: Validar que una estrategia autorizada puede ser modificada.

**Diagrama de flujo**:
```
Autorizada (concepto: Registro) → Modificar → (nueva estrategia con concepto "Modificación")
```

#### **Paso 1: Creación de Modificación**

**Usuario**: Usuario de Institución

**Prerrequisito**: Tener una estrategia en estado "Autorizada" con concepto diferente a "Cancelación"

**Acciones a probar**:

1. Localizar una estrategia en estado "Autorizada"
2. Verificar que el botón "Modificar Estrategia" esté visible
3. Hacer clic en "Modificar Estrategia"
4. Confirmar el modal
5. Se crea una nueva estrategia (duplicado) con:
   - Concepto: "Modificación"
   - Estado: "Creada"
   - Referencia a la estrategia original
6. Editar la nueva estrategia para realizar los cambios necesarios
7. Enviar a CS

**Validaciones**:
- ✓ El botón "Modificar Estrategia" solo aparece si:
  - El estado es "Autorizada"
  - El concepto NO es "Cancelación"
  - Es la última estrategia para esa institución y año
- ✓ Se crea una nueva estrategia con concepto "Modificación"
- ✓ La nueva estrategia contiene todos los datos de la original (campañas incluidas)
- ✓ La nueva estrategia está en estado "Creada" y es editable
- ✓ Se mantiene referencia a la estrategia original
- ✓ Se puede enviar a través del flujo normal de revisión

---

#### **Paso 2: Flujo de Aprobación de Modificación**

**Usuarios**: Coordinadora de Sector y Usuario DGNC

**Acciones a probar**:

1. La modificación sigue el flujo normal:
   - Creada → Enviado a CS → Aceptada CS → Enviada a DGNC → Autorizada

**Validaciones**:
- ✓ El flujo de aprobación es idéntico al flujo de registro
- ✓ En la tabla, la estrategia se muestra con el concepto "Modificación"

---

### 5.6 Flujo de Cancelación

**Objetivo**: Validar que una estrategia autorizada puede ser cancelada.

**Diagrama de flujo**:
```
Autorizada (concepto: Registro/Modificación) → Cancelar → (nueva estrategia con concepto "Cancelación")
```

#### **Paso 1: Creación de Cancelación**

**Usuario**: Usuario de Institución

**Prerrequisito**: Tener una estrategia en estado "Autorizada" con concepto diferente a "Cancelación"

**Acciones a probar**:

1. Localizar una estrategia en estado "Autorizada"
2. Verificar que el botón "Cancelar Estrategia" esté visible
3. Hacer clic en "Cancelar Estrategia"
4. Confirmar el modal
5. Se crea una nueva estrategia (duplicado) con:
   - Concepto: "Cancelación"
   - Estado: "Creada"
   - Referencia a la estrategia original
6. Enviar directamente a CS (no requiere edición)

**Validaciones**:
- ✓ El botón "Cancelar Estrategia" solo aparece si:
  - El estado es "Autorizada"
  - El concepto NO es "Cancelación"
  - Es la última estrategia para esa institución y año
- ✓ Se crea una nueva estrategia con concepto "Cancelación"
- ✓ La nueva estrategia contiene todos los datos de la original
- ✓ La nueva estrategia está en estado "Creada"
- ✓ Se puede enviar a CS

---

#### **Paso 2: Flujo de Aprobación de Cancelación**

**Usuarios**: Coordinadora de Sector y Usuario DGNC

**Acciones a probar**:

1. La cancelación sigue el flujo normal:
   - Creada → Enviado a CS → Aceptada CS → Enviada a DGNC → Autorizada
2. Una vez autorizada la cancelación, verificar que:
   - NO aparece el botón "Modificar Estrategia"
   - NO aparece el botón "Cancelar Estrategia"

**Validaciones**:
- ✓ El flujo de aprobación es idéntico al flujo de registro
- ✓ Una vez autorizada una cancelación, no se pueden crear más modificaciones o cancelaciones de esa estrategia

---

## Validaciones Importantes

### 6.1 Validación de Presupuesto

**Validación crítica al enviar a CS**:

```
Suma de todas las campañas = Presupuesto Anual
```

**Cómo probar**:

1. Crear una estrategia con presupuesto anual de $1,000,000
2. Crear 2 campañas:
   - Campaña 1: $500,000 total
   - Campaña 2: $400,000 total
3. Intentar enviar a CS
4. **Resultado esperado**: Error, la suma ($900,000) no es igual al presupuesto ($1,000,000)
5. Corregir para que sume exactamente $1,000,000
6. Enviar a CS
7. **Resultado esperado**: Éxito

---

### 6.2 Validación de Radios Comunitarias

**Validación informativa al enviar a CS**:

```
Radios Comunitarias (Medios Digitales) >= 1% del Presupuesto Anual
```

**Cómo probar**:

1. Crear una estrategia con presupuesto anual de $1,000,000
2. Crear una campaña con:
   - Medios Digitales (Radios Comunitarias): $5,000 (0.5%)
   - Otros medios: $995,000
3. Enviar a CS
4. **Resultado esperado**: Advertencia amarilla pero permite enviar

---

### 6.3 Validación de Última Estrategia

**Regla**: Solo la última estrategia de una institución para un año específico y partida presupuestal puede ser modificada o tener acciones.

**Cómo probar**:

1. Crear una estrategia para 2025 (Estrategia A)
2. Autorizar la estrategia
3. Crear una modificación (Estrategia B)
4. Verificar que en la Estrategia A ya no aparecen los botones de acción
5. Verificar que en la Estrategia B sí aparecen los botones de acción

---

## Casos de Prueba Específicos

### 7.1 Prueba de Filtrado por Institución

**Usuario**: institution_user

**Pasos**:

1. Iniciar sesión como usuario de Institución A
2. Navegar a la lista de estrategias
3. Verificar que SOLO se vean estrategias de la Institución A
4. Intentar acceder directamente a una URL de estrategia de Institución B
5. **Resultado esperado**: Error 403 o redirección

---

### 7.2 Prueba de Filtrado por Sector

**Usuario**: sector_coordinator

**Pasos**:

1. Iniciar sesión como coordinadora del Sector 1
2. Navegar a la lista de estrategias
3. Verificar que SOLO se vean estrategias de instituciones del Sector 1
4. Intentar acceder directamente a una URL de estrategia de Sector 2
5. **Resultado esperado**: Error 403 o redirección

---

### 7.3 Prueba de Visibilidad de Acciones según Rol

**Matriz de visibilidad**:

| Acción | institution_user | sector_coordinator | dgnc_user | super_admin |
|--------|------------------|-------------------|-----------|-------------|
| Crear | ✓ | ✗ | ✗ | ✗ |
| Editar (Creada/Rechazada CS/Rechazada DGNC) | ✓ | ✗ | ✗ | ✗ |
| Eliminar | ✗ | ✗ | ✗ | ✓ |
| Enviar a CS | ✓ | ✗ | ✗ | ✗ |
| Evaluar Estrategia (Aceptar/Rechazar CS) | ✗ | ✓ | ✗ | ✗ |
| Enviar a DGNC | ✗ | ✓ | ✗ | ✗ |
| Autorizar DGNC | ✗ | ✗ | ✓ | ✗ |
| Rechazar DGNC | ✗ | ✗ | ✓ | ✗ |
| Observar DGNC | ✗ | ✗ | ✓ | ✗ |
| Modificar Estrategia (Autorizada) | ✓ | ✗ | ✗ | ✗ |
| Solventar Estrategia (Observada) | ✓ | ✗ | ✗ | ✗ |
| Cancelar Estrategia (Autorizada) | ✓ | ✗ | ✗ | ✗ |
| Cambios Estrategia (editar campos críticos) | ✗ | ✗ | ✗ | ✓ |
| Cargar Oficio DGNC | ✗ | ✗ | ✓ | ✓ |
| Ver Oficios DGNC | ✗ | ✗ | ✓ | ✓ |

**Pasos**:

1. Crear una estrategia en cada estado
2. Probar con cada rol que solo aparezcan las acciones correspondientes según la matriz

---

### 7.4 Prueba de Exportación PDF

**Usuario**: Todos los roles

**Pasos**:

1. Crear una estrategia completa con todas las secciones
2. Hacer clic en "Exportar PDF"
3. Verificar que el PDF se descargue correctamente
4. Abrir el PDF y verificar que contenga:
   - Logos configurados en el sistema
   - Datos generales de la institución
   - Objetivos
   - Campañas con todas sus versiones
   - Presupuesto detallado
   - Firmas

**Validaciones**:
- ✓ El PDF se genera sin errores
- ✓ Todos los datos se muestran correctamente
- ✓ El formato es correcto y legible

---

### 7.5 Prueba de Partidas Presupuestales

**Usuario**: institution_user

**Pasos**:

1. Crear una estrategia en "Comunicación Social" (36101)
2. Crear una estrategia en "Promoción y Publicidad" (36201)
3. Verificar que ambas estrategias:
   - Están separadas en navegación
   - Tienen campos específicos diferentes:
     - 36101: Ejes del Plan Nacional
     - 36201: Entorno de Mercado y Metas Generales
   - Se muestran en listas independientes

**Validaciones**:
- ✓ Las partidas están completamente separadas
- ✓ Los campos específicos se muestran correctamente
- ✓ No hay cruces entre partidas

---

### 7.6 Prueba de Oficios DGNC

**Usuario**: dgnc_user

**Pasos**:

1. Localizar una estrategia
2. Hacer clic en "Cargar Oficio DGNC"
3. Subir un archivo PDF con información del oficio:
   - Número de oficio
   - Fecha
   - Archivo PDF
4. Guardar
5. Verificar que el contador de oficios se incremente
6. Hacer clic en "Ver Oficios DGNC"
7. Verificar que se liste el oficio cargado
8. Descargar el PDF del oficio

**Validaciones**:
- ✓ Solo usuarios DGNC y super_admin pueden cargar oficios
- ✓ Los oficios se asocian correctamente a la estrategia
- ✓ El contador refleja la cantidad correcta
- ✓ Los PDFs se pueden descargar

---

### 7.7 Prueba de Filtro de Año

**Usuario**: Todos los roles

**Pasos**:

1. Crear estrategias para diferentes años (2025, 2026, 2027)
2. En la lista de estrategias, aplicar filtro de año 2025
3. Verificar que solo se muestren estrategias de 2025
4. Cambiar filtro a 2026
5. Verificar que solo se muestren estrategias de 2026
6. Quitar el filtro
7. Verificar comportamiento por defecto (año actual)

**Validaciones**:
- ✓ El filtro funciona correctamente
- ✓ Por defecto se muestra el año actual
- ✓ El filtro se mantiene al navegar entre páginas

---

### 7.8 Prueba de Edición de Campos Críticos (Super Admin)

**Usuario**: super_admin

**Pasos**:

1. Localizar cualquier estrategia
2. Hacer clic en "Cambios Estrategia"
3. Modificar:
   - Fecha de elaboración
   - Fecha de envío DGNC
   - Estado de la estrategia
4. Guardar cambios
5. Verificar que los cambios se reflejen correctamente

**Validaciones**:
- ✓ Solo super_admin puede ver y usar esta acción
- ✓ Los cambios se guardan correctamente
- ✓ Se puede cambiar manualmente el estado (útil para pruebas o correcciones)

---

## Resumen de Flujos por Concepto

### Concepto: Registro

```
1. Crear estrategia (estado: Creada, concepto: Registro)
2. Enviar a CS → Enviado a CS
3. CS evalúa → Aceptada CS o Rechazada CS
4. Si Aceptada CS → CS envía a DGNC → Enviada a DGNC
5. DGNC evalúa → Autorizada, Rechazada DGNC u Observada DGNC
```

### Concepto: Modificación

```
1. Estrategia con estado Autorizada
2. Usuario institución hace clic en "Modificar Estrategia"
3. Se crea nueva estrategia (concepto: Modificación, estado: Creada)
4. Sigue flujo normal de aprobación
```

### Concepto: Solventación

```
1. Estrategia con estado Observada DGNC
2. Usuario institución hace clic en "Solventar Estrategia"
3. Se crea nueva estrategia (concepto: Solventación, estado: Creada)
4. Sigue flujo normal de aprobación
```

### Concepto: Cancelación

```
1. Estrategia con estado Autorizada
2. Usuario institución hace clic en "Cancelar Estrategia"
3. Se crea nueva estrategia (concepto: Cancelación, estado: Creada)
4. Sigue flujo normal de aprobación
5. Una vez autorizada, NO permite más modificaciones o cancelaciones
```

---

## Notas Finales

1. **Solo la última estrategia**: Solo la estrategia más reciente para una institución, año y partida puede tener acciones disponibles.

2. **Referencia entre versiones**: Todas las modificaciones, solventaciones y cancelaciones mantienen una referencia a la estrategia original mediante `estrategia_original_id`.

3. **Validación de presupuesto**: Es obligatorio que la suma de campañas sea igual al presupuesto anual al enviar a CS.

4. **Advertencia de radios comunitarias**: Es una advertencia informativa, no bloquea el envío.

5. **Estados no editables**: Una vez enviada a CS, la estrategia no es editable hasta que sea rechazada.

6. **Super Admin**: Puede ver todas las estrategias, eliminarlas y editar campos críticos, pero NO puede editar el contenido de las estrategias.

---

**Fecha de creación**: 2025-11-26
**Sistema**: NSINC - Sistema de Estrategias de Comunicación
**Framework**: Laravel 12 + Filament 3.3
