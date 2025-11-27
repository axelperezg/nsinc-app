# Matriz de Compatibilidad de Estados y Acciones - Sistema NSINC

Esta matriz muestra qué acciones están disponibles para cada estado de estrategia y qué rol puede ejecutarlas.

## Leyenda

- ✅ **Disponible**: La acción está disponible en este estado
- ❌ **No disponible**: La acción NO está disponible en este estado
- ⚠️ **Condicional**: La acción está disponible bajo ciertas condiciones

---

## Matriz Principal: Acciones vs Estados

### Sección 1: Acciones CRUD Básicas

| Acción | Creada | Enviado a CS | Aceptada CS | Rechazada CS | Enviada a DGNC | Autorizada | Rechazada DGNC | Observada DGNC |
|--------|--------|--------------|-------------|--------------|----------------|------------|----------------|----------------|
| **Ver** | ✅ Todos | ✅ Todos | ✅ Todos | ✅ Todos | ✅ Todos | ✅ Todos | ✅ Todos | ✅ Todos |
| **Editar** | ✅ IU | ❌ | ❌ | ✅ IU | ❌ | ❌ | ✅ IU | ❌ |
| **Eliminar** | ✅ SA | ✅ SA | ✅ SA | ✅ SA | ✅ SA | ✅ SA | ✅ SA | ✅ SA |
| **Exportar PDF** | ✅ Todos | ✅ Todos | ✅ Todos | ✅ Todos | ✅ Todos | ✅ Todos | ✅ Todos | ✅ Todos |

**Roles**:
- **IU**: institution_user (Usuario de Institución)
- **CS**: sector_coordinator (Coordinadora de Sector)
- **DG**: dgnc_user (Usuario DGNC)
- **SA**: super_admin (Super Administrador)
- **Todos**: Todos los roles con acceso a la estrategia

---

### Sección 2: Acciones de Flujo de Trabajo (Usuario de Institución)

| Acción | Creada | Enviado a CS | Aceptada CS | Rechazada CS | Enviada a DGNC | Autorizada | Rechazada DGNC | Observada DGNC |
|--------|--------|--------------|-------------|--------------|----------------|------------|----------------|----------------|
| **Enviar a CS** | ✅ IU | ❌ | ❌ | ✅ IU | ❌ | ❌ | ✅ IU | ❌ |
| **Modificar Estrategia** | ❌ | ❌ | ❌ | ❌ | ❌ | ⚠️ IU | ❌ | ❌ |
| **Solventar Estrategia** | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ | ✅ IU |
| **Cancelar Estrategia** | ❌ | ❌ | ❌ | ❌ | ❌ | ⚠️ IU | ❌ | ❌ |

**Condiciones especiales**:

- **Modificar Estrategia** (⚠️):
  - Solo si es la última estrategia para la institución/año/partida
  - Solo si el concepto NO es "Cancelación"

- **Cancelar Estrategia** (⚠️):
  - Solo si es la última estrategia para la institución/año/partida
  - Solo si el concepto NO es "Cancelación"

---

### Sección 3: Acciones de Flujo de Trabajo (Coordinadora de Sector)

| Acción | Creada | Enviado a CS | Aceptada CS | Rechazada CS | Enviada a DGNC | Autorizada | Rechazada DGNC | Observada DGNC |
|--------|--------|--------------|-------------|--------------|----------------|------------|----------------|----------------|
| **Evaluar Estrategia** | ❌ | ✅ CS | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ |
| **Enviar a DGNC** | ❌ | ❌ | ✅ CS | ❌ | ❌ | ❌ | ❌ | ❌ |

**Nota**: La acción "Evaluar Estrategia" permite cambiar el estado a:
- "Aceptada CS"
- "Rechazada CS"

---

### Sección 4: Acciones de Flujo de Trabajo (Usuario DGNC)

| Acción | Creada | Enviado a CS | Aceptada CS | Rechazada CS | Enviada a DGNC | Autorizada | Rechazada DGNC | Observada DGNC |
|--------|--------|--------------|-------------|--------------|----------------|------------|----------------|----------------|
| **Autorizar DGNC** | ❌ | ❌ | ❌ | ❌ | ✅ DG | ❌ | ❌ | ❌ |
| **Rechazar DGNC** | ❌ | ❌ | ❌ | ❌ | ✅ DG | ❌ | ❌ | ❌ |
| **Observar DGNC** | ❌ | ❌ | ❌ | ❌ | ✅ DG | ❌ | ❌ | ❌ |
| **Cargar Oficio DGNC** | ✅ DG | ✅ DG | ✅ DG | ✅ DG | ✅ DG | ✅ DG | ✅ DG | ✅ DG |
| **Ver Oficios DGNC** | ✅ DG/SA | ✅ DG/SA | ✅ DG/SA | ✅ DG/SA | ✅ DG/SA | ✅ DG/SA | ✅ DG/SA | ✅ DG/SA |

---

### Sección 5: Acciones Administrativas (Super Admin)

| Acción | Creada | Enviado a CS | Aceptada CS | Rechazada CS | Enviada a DGNC | Autorizada | Rechazada DGNC | Observada DGNC |
|--------|--------|--------------|-------------|--------------|----------------|------------|----------------|----------------|
| **Cambios Estrategia** | ✅ SA | ✅ SA | ✅ SA | ✅ SA | ✅ SA | ✅ SA | ✅ SA | ✅ SA |

**Nota**: "Cambios Estrategia" permite editar campos críticos:
- Fecha de elaboración
- Fecha de envío a DGNC
- Estado de la estrategia (puede cambiarlo manualmente a cualquier estado)

---

## Matriz Inversa: Estados vs Transiciones

Esta tabla muestra a qué estados puede cambiar cada estado actual y quién puede hacer la transición.

| Estado Actual | Puede cambiar a | Rol que ejecuta | Acción |
|---------------|-----------------|-----------------|---------|
| **Creada** | Enviado a CS | institution_user | Enviar a CS |
| **Enviado a CS** | Aceptada CS | sector_coordinator | Evaluar Estrategia → Aceptar |
| **Enviado a CS** | Rechazada CS | sector_coordinator | Evaluar Estrategia → Rechazar |
| **Rechazada CS** | Enviado a CS | institution_user | Editar + Enviar a CS |
| **Aceptada CS** | Enviada a DGNC | sector_coordinator | Enviar a DGNC |
| **Enviada a DGNC** | Autorizada | dgnc_user | Autorizar DGNC |
| **Enviada a DGNC** | Rechazada DGNC | dgnc_user | Rechazar DGNC |
| **Enviada a DGNC** | Observada DGNC | dgnc_user | Observar DGNC |
| **Rechazada DGNC** | Enviado a CS | institution_user | Editar + Enviar a CS |
| **Observada DGNC** | (nueva estrategia Creada) | institution_user | Solventar Estrategia |
| **Autorizada** | (nueva estrategia Creada) | institution_user | Modificar Estrategia |
| **Autorizada** | (nueva estrategia Creada) | institution_user | Cancelar Estrategia |
| **Cualquier estado** | Cualquier estado | super_admin | Cambios Estrategia |

---

## Matriz por Rol: ¿Qué puede hacer cada rol?

### institution_user (Usuario de Institución)

| Estado | Acciones Disponibles | Observaciones |
|--------|---------------------|---------------|
| **Creada** | • Ver<br>• Editar<br>• Exportar PDF<br>• Enviar a CS | Estado inicial después de crear |
| **Enviado a CS** | • Ver<br>• Exportar PDF | No puede editar ni hacer cambios |
| **Aceptada CS** | • Ver<br>• Exportar PDF | Esperando que CS envíe a DGNC |
| **Rechazada CS** | • Ver<br>• Editar<br>• Exportar PDF<br>• Enviar a CS | Puede corregir y reenviar |
| **Enviada a DGNC** | • Ver<br>• Exportar PDF | Esperando decisión de DGNC |
| **Autorizada** | • Ver<br>• Exportar PDF<br>• Modificar Estrategia*<br>• Cancelar Estrategia* | *Solo si no es concepto "Cancelación" |
| **Rechazada DGNC** | • Ver<br>• Editar<br>• Exportar PDF<br>• Enviar a CS | Puede corregir y reenviar |
| **Observada DGNC** | • Ver<br>• Exportar PDF<br>• Solventar Estrategia | Debe crear solventación |

---

### sector_coordinator (Coordinadora de Sector)

| Estado | Acciones Disponibles | Observaciones |
|--------|---------------------|---------------|
| **Creada** | • Ver<br>• Exportar PDF | Solo lectura |
| **Enviado a CS** | • Ver<br>• Exportar PDF<br>• Evaluar Estrategia | Puede aceptar o rechazar |
| **Aceptada CS** | • Ver<br>• Exportar PDF<br>• Enviar a DGNC | Debe enviar a DGNC |
| **Rechazada CS** | • Ver<br>• Exportar PDF | Solo lectura |
| **Enviada a DGNC** | • Ver<br>• Exportar PDF | Esperando decisión de DGNC |
| **Autorizada** | • Ver<br>• Exportar PDF | Solo lectura |
| **Rechazada DGNC** | • Ver<br>• Exportar PDF | Solo lectura |
| **Observada DGNC** | • Ver<br>• Exportar PDF | Solo lectura |

**Restricciones**:
- Solo ve estrategias de instituciones de su sector
- No puede editar estrategias en ningún estado

---

### dgnc_user (Usuario DGNC)

| Estado | Acciones Disponibles | Observaciones |
|--------|---------------------|---------------|
| **Creada** | • Ver<br>• Exportar PDF<br>• Cargar Oficio DGNC<br>• Ver Oficios DGNC | Solo lectura |
| **Enviado a CS** | • Ver<br>• Exportar PDF<br>• Cargar Oficio DGNC<br>• Ver Oficios DGNC | Solo lectura |
| **Aceptada CS** | • Ver<br>• Exportar PDF<br>• Cargar Oficio DGNC<br>• Ver Oficios DGNC | Solo lectura |
| **Rechazada CS** | • Ver<br>• Exportar PDF<br>• Cargar Oficio DGNC<br>• Ver Oficios DGNC | Solo lectura |
| **Enviada a DGNC** | • Ver<br>• Exportar PDF<br>• Autorizar DGNC<br>• Rechazar DGNC<br>• Observar DGNC<br>• Cargar Oficio DGNC<br>• Ver Oficios DGNC | Puede tomar decisión final |
| **Autorizada** | • Ver<br>• Exportar PDF<br>• Cargar Oficio DGNC<br>• Ver Oficios DGNC | Solo lectura |
| **Rechazada DGNC** | • Ver<br>• Exportar PDF<br>• Cargar Oficio DGNC<br>• Ver Oficios DGNC | Solo lectura |
| **Observada DGNC** | • Ver<br>• Exportar PDF<br>• Cargar Oficio DGNC<br>• Ver Oficios DGNC | Solo lectura |

**Privilegios**:
- Ve todas las estrategias de todas las instituciones
- Puede cargar oficios DGNC en cualquier estado
- No puede editar estrategias directamente

---

### super_admin (Super Administrador)

| Estado | Acciones Disponibles | Observaciones |
|--------|---------------------|---------------|
| **Todos** | • Ver<br>• Exportar PDF<br>• Eliminar<br>• Cambios Estrategia<br>• Ver Oficios DGNC | Control total administrativo |

**Privilegios especiales**:
- Ve todas las estrategias de todas las instituciones
- Puede eliminar cualquier estrategia (solo la última por institución/año/partida)
- Puede cambiar manualmente el estado a través de "Cambios Estrategia"
- Puede editar fecha de elaboración y fecha de envío a DGNC
- NO puede editar el contenido de las estrategias (campañas, presupuesto, etc.)

---

## Matriz por Concepto: Acciones según tipo de estrategia

### Concepto: Registro

| Estado | Acciones Especiales |
|--------|---------------------|
| **Autorizada** | • Modificar Estrategia ✅<br>• Cancelar Estrategia ✅ |

---

### Concepto: Modificación

| Estado | Acciones Especiales |
|--------|---------------------|
| **Autorizada** | • Modificar Estrategia ✅<br>• Cancelar Estrategia ✅ |

---

### Concepto: Solventación

| Estado | Acciones Especiales |
|--------|---------------------|
| **Autorizada** | • Modificar Estrategia ✅<br>• Cancelar Estrategia ✅ |

---

### Concepto: Cancelación

| Estado | Acciones Especiales |
|--------|---------------------|
| **Autorizada** | • Modificar Estrategia ❌<br>• Cancelar Estrategia ❌ |

**Regla importante**: Una vez que una cancelación está autorizada, NO se pueden crear más modificaciones ni cancelaciones.

---

## Diagrama de Flujo Completo con Estados

```
┌─────────────────────────────────────────────────────────────────┐
│                    INSTITUCIÓN (institution_user)                │
└─────────────────────────────────────────────────────────────────┘
                                ↓
                          [CREADA]
                         (Editable)
                                ↓
                    Acción: Enviar a CS
                                ↓
┌─────────────────────────────────────────────────────────────────┐
│              COORDINADORA DE SECTOR (sector_coordinator)         │
└─────────────────────────────────────────────────────────────────┘
                                ↓
                        [ENVIADO A CS]
                                ↓
                   Acción: Evaluar Estrategia
                                ↓
                ┌───────────────┴───────────────┐
                ↓                               ↓
         [ACEPTADA CS]                  [RECHAZADA CS]
                ↓                               ↓
    Acción: Enviar a DGNC          Vuelve a INSTITUCIÓN
                ↓                     (puede editar y reenviar)
                │
                ↓
┌─────────────────────────────────────────────────────────────────┐
│                      DGNC (dgnc_user)                            │
└─────────────────────────────────────────────────────────────────┘
                ↓
        [ENVIADA A DGNC]
                ↓
      Acciones: Autorizar / Rechazar / Observar
                ↓
    ┌───────────┴───────────┬───────────────────┐
    ↓                       ↓                   ↓
[AUTORIZADA]        [RECHAZADA DGNC]    [OBSERVADA DGNC]
    ↓                       ↓                   ↓
Permite:            Vuelve a INSTITUCIÓN   Permite:
• Modificar         (puede editar          • Solventar
• Cancelar          y reenviar)            (crea nueva estrategia)
(crea nueva                                     ↓
estrategia)                             Nueva [CREADA] concepto
                                        "Solventación"
    ↓
Nueva [CREADA]
concepto "Modificación"
o "Cancelación"
```

---

## Reglas Globales de Visibilidad

### Regla 1: Solo la última estrategia tiene acciones

**Condición**: Solo la estrategia más reciente (última creada) para una institución, año y partida presupuestal puede tener acciones disponibles.

**Ejemplo**:
1. Estrategia 2025 #1 (Registro) → Autorizada
2. Estrategia 2025 #2 (Modificación de #1) → Creada
3. Solo en #2 aparecen las acciones, #1 es solo lectura

---

### Regla 2: Validación de presupuesto al enviar

**Estados afectados**: Creada, Rechazada CS, Rechazada DGNC

**Condición**: Al ejecutar "Enviar a CS", se valida:
- ✅ Suma de campañas = Presupuesto anual (OBLIGATORIO)
- ⚠️ Radios Comunitarias >= 1% (ADVERTENCIA)

**Si falla validación obligatoria**: No cambia de estado, muestra error.

---

### Regla 3: Estados bloqueados (no editables)

**Estados bloqueados**:
- Enviado a CS
- Aceptada CS
- Enviada a DGNC
- Autorizada
- Observada DGNC

**Estados editables**:
- Creada
- Rechazada CS
- Rechazada DGNC

---

### Regla 4: Filtrado por institución/sector

| Rol | Ve estrategias de |
|-----|-------------------|
| institution_user | Solo su institución |
| sector_coordinator | Solo instituciones de su sector |
| dgnc_user | Todas las instituciones |
| super_admin | Todas las instituciones |

---

## Casos Especiales

### Caso 1: Estrategia autorizada con concepto "Cancelación"

| Acción | Disponible | Razón |
|--------|------------|-------|
| Modificar Estrategia | ❌ | Una cancelación autorizada no puede modificarse |
| Cancelar Estrategia | ❌ | Ya está cancelada |
| Ver | ✅ | Siempre se puede ver |
| Exportar PDF | ✅ | Siempre se puede exportar |

---

### Caso 2: Usuario sin institución asignada

| Rol | Comportamiento |
|-----|----------------|
| institution_user | No puede crear estrategias, no ve ninguna |
| sector_coordinator | No puede crear estrategias, no ve ninguna |
| dgnc_user | Puede trabajar normalmente |
| super_admin | Puede trabajar normalmente |

---

### Caso 3: Múltiples versiones de la misma estrategia

**Escenario**:
1. Estrategia 2025 #1 (Registro) → Autorizada
2. Estrategia 2025 #2 (Modificación) → Autorizada
3. Estrategia 2025 #3 (Modificación) → Creada

**Acciones disponibles**:
- #1: Solo lectura
- #2: Solo lectura
- #3: Todas las acciones según estado "Creada"

---

## Checklist de Pruebas por Estado

### Estado: Creada

- [ ] institution_user puede ver
- [ ] institution_user puede editar
- [ ] institution_user puede enviar a CS
- [ ] sector_coordinator puede ver (solo lectura)
- [ ] dgnc_user puede ver (solo lectura)
- [ ] super_admin puede ver, eliminar y cambiar estado

---

### Estado: Enviado a CS

- [ ] institution_user puede ver (solo lectura)
- [ ] sector_coordinator puede ver y evaluar
- [ ] sector_coordinator puede aceptar (cambiar a "Aceptada CS")
- [ ] sector_coordinator puede rechazar (cambiar a "Rechazada CS")
- [ ] dgnc_user puede ver (solo lectura)
- [ ] super_admin puede ver, eliminar y cambiar estado

---

### Estado: Aceptada CS

- [ ] institution_user puede ver (solo lectura)
- [ ] sector_coordinator puede ver y enviar a DGNC
- [ ] Al enviar a DGNC se registra fecha_envio_dgnc
- [ ] dgnc_user puede ver (solo lectura)
- [ ] super_admin puede ver, eliminar y cambiar estado

---

### Estado: Rechazada CS

- [ ] institution_user puede ver, editar y enviar a CS
- [ ] sector_coordinator puede ver (solo lectura)
- [ ] dgnc_user puede ver (solo lectura)
- [ ] super_admin puede ver, eliminar y cambiar estado

---

### Estado: Enviada a DGNC

- [ ] institution_user puede ver (solo lectura)
- [ ] sector_coordinator puede ver (solo lectura)
- [ ] dgnc_user puede ver y autorizar
- [ ] dgnc_user puede rechazar
- [ ] dgnc_user puede observar
- [ ] super_admin puede ver, eliminar y cambiar estado

---

### Estado: Autorizada

- [ ] institution_user puede ver y modificar (si no es cancelación)
- [ ] institution_user puede cancelar (si no es cancelación)
- [ ] sector_coordinator puede ver (solo lectura)
- [ ] dgnc_user puede ver (solo lectura)
- [ ] super_admin puede ver, eliminar y cambiar estado

---

### Estado: Rechazada DGNC

- [ ] institution_user puede ver, editar y enviar a CS
- [ ] sector_coordinator puede ver (solo lectura)
- [ ] dgnc_user puede ver (solo lectura)
- [ ] super_admin puede ver, eliminar y cambiar estado

---

### Estado: Observada DGNC

- [ ] institution_user puede ver y solventar
- [ ] Al solventar se crea nueva estrategia con concepto "Solventación"
- [ ] sector_coordinator puede ver (solo lectura)
- [ ] dgnc_user puede ver (solo lectura)
- [ ] super_admin puede ver, eliminar y cambiar estado

---

**Fecha de creación**: 2025-11-26
**Sistema**: NSINC - Sistema de Estrategias de Comunicación
**Framework**: Laravel 12 + Filament 3.3
