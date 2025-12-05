# Guía para Configurar Subdominio en Ploi.io

## Problema Actual

El subdominio `nsinc.hwa-company.com` no está resolviendo en DNS. El dominio principal `hwa-company.com` sí existe y apunta a `178.156.196.178`.

## Solución: Configurar el Subdominio en Ploi.io

### Paso 1: Verificar el Sitio en Ploi.io

1. Inicia sesión en tu panel de Ploi.io
2. Ve a tu servidor
3. Busca el sitio `nsinc.hwa-company.com` o créalo si no existe

### Paso 2: Agregar el Dominio/Subdominio

1. En la sección **"Domains"** del sitio
2. Haz clic en **"Add Domain"** o **"Add Subdomain"**
3. Ingresa: `nsinc.hwa-company.com`
4. Ploi.io te mostrará las instrucciones de configuración DNS

### Paso 3: Configurar DNS en tu Proveedor de Dominio

Tienes dos opciones:

#### Opción A: DNS Automático de Ploi.io (Recomendado)

Si Ploi.io gestiona el DNS automáticamente:
- Solo espera a que se propague (5-30 minutos)
- Ploi creará automáticamente el registro A o CNAME necesario

#### Opción B: Configuración Manual

Si necesitas configurar manualmente en tu proveedor de dominio (donde está registrado `hwa-company.com`):

1. **Accede al panel de DNS de tu proveedor de dominio**
2. **Agrega un registro DNS:**

   **Tipo:** `A` o `CNAME`
   
   **Nombre/Host:** `nsinc`
   
   **Valor/Destino:** 
   - Si es registro A: La IP del servidor de Ploi (generalmente la misma que `hwa-company.com`: `178.156.196.178`)
   - Si es CNAME: El dominio que Ploi te indique (ej: `nsinc.hwa-company.com.ploi.site` o similar)
   
   **TTL:** 300-600 segundos (para propagación más rápida)

### Paso 4: Verificar la Configuración

Después de configurar, verifica con:

```powershell
# En PowerShell
nslookup nsinc.hwa-company.com

# O usar herramientas online:
# - https://www.whatsmydns.net/
# - https://dnschecker.org/
```

### Paso 5: Esperar la Propagación

- **Tiempo típico:** 5-30 minutos
- **Máximo:** 24-48 horas (raro)

## Verificación en Ploi.io

1. Ve a tu sitio en Ploi.io
2. Verifica que el dominio aparezca en la lista de dominios
3. Asegúrate de que el estado sea "Active" o "Verified"
4. Si hay un estado de "Pending", espera a que se verifique

## Solución de Problemas

### Si el subdominio no aparece en Ploi.io:

1. **Crea un nuevo sitio** con el dominio `nsinc.hwa-company.com`
2. O **agrega el dominio** al sitio existente desde la sección "Domains"

### Si el DNS no se propaga después de 2 horas:

1. Verifica que el registro DNS esté correctamente configurado
2. Verifica que el TTL no sea muy alto (recomendado: 300-600 segundos)
3. Limpia la caché DNS local:
   ```powershell
   ipconfig /flushdns
   ```
4. Contacta al soporte de Ploi.io si el problema persiste

### Si necesitas usar el sitio antes de que se propague:

Puedes acceder temporalmente usando el dominio de Ploi.io (si está disponible):
- Ejemplo: `nsinc-xxxxx.ploi.site` (donde xxxxx es un identificador)

## Notas Importantes

- ⚠️ El dominio principal debe estar correctamente configurado primero
- 🔒 Asegúrate de que el SSL/HTTPS esté habilitado en Ploi.io para el subdominio
- 📝 Los cambios de DNS pueden tardar en propagarse según tu ubicación geográfica
- 🔄 Después de configurar el DNS, puede tomar hasta 48 horas para propagarse completamente

## Comandos Útiles para Verificar

```powershell
# Verificar resolución DNS
nslookup nsinc.hwa-company.com

# Verificar con servidor DNS público
nslookup nsinc.hwa-company.com 8.8.8.8

# Limpiar caché DNS local
ipconfig /flushdns

# Verificar conectividad
ping nsinc.hwa-company.com
```

## Contacto

Si después de seguir estos pasos el problema persiste:
- Revisa la documentación de Ploi.io sobre dominios
- Contacta al soporte de Ploi.io
- Verifica con tu proveedor de dominio que los registros DNS estén correctos

