# Teralinde CRM en Supabase

## Estado comprobado el 13 de octubre de 2026

Se creó el esquema privado `teralinde_crm` en la base PostgreSQL existente del proyecto Supabase `EcoSphere` (`kslzmrddrhfyyrxyfmbw`). Es un esquema separado, no un proyecto ni una instancia independiente. Comparte recursos, disponibilidad y respaldos con EcoSphere.

El conector permite administrar SQL y migraciones, pero `get_cost` y `confirm_cost` no estaban disponibles en el servidor. No se creó un proyecto adicional ni se contrataron recursos de pago. Se evaluó Render como alternativa; su PostgreSQL gratuito caduca a los 30 días.

No se alteraron tablas, políticas ni datos de los esquemas existentes de EcoSphere.

## Estructura

11 tablas: `migrations`, `users`, `password_reset_tokens`, `sessions`, `cache`, `cache_locks`, `jobs`, `job_batches`, `failed_jobs`, `clientes` y `visitas`.

La estructura parte de las seis migraciones Laravel de la revisión `0889c36749afec2b9f2007b8a4b699c70f447691`. Se registraron esas migraciones en `teralinde_crm.migrations` para evitar que Laravel intente crear de nuevo las mismas tablas. Los índices de visitas cubren la referencia al cliente y los filtros de agenda.

El esquema ya se aplicó mediante la migración Supabase `teralinde_crm_laravel_baseline`. `database/bootstrap/teralinde_supabase.sql` conserva la SQL aplicada para revisión o para un proyecto nuevo. **No volver a ejecutarla en esta base:** no es un script idempotente.

## Acceso

Todas las tablas tienen RLS activado y forzado. `anon`, `authenticated` y `service_role` no tienen acceso al esquema ni a sus tablas. No se expone el esquema a la Data API.

`teralinde_crm_app` es un rol exclusivo del futuro backend Laravel: puede operar los datos del CRM, pero no crear tablas ni modificar el historial de migraciones. Su política RLS autoriza al servidor a manejar el conjunto del CRM; la autorización de cada usuario o negocio debe resolverse y comprobarse en Laravel antes de abrir el servicio a clientes.

El rol permanece **NOLOGIN**, sin contraseña. El despliegue deberá generar una credencial exclusiva, guardarla como secreto del servidor y habilitar el login. Nunca usar las credenciales administrativas de EcoSphere en el cliente web.

## Conexión del futuro backend

Laravel admite PostgreSQL mediante PDO. El servidor necesitará PHP, sus dependencias del proyecto y la extensión `pdo_pgsql`.

Conservar la configuración completa y `APP_KEY` en el `.env` privado de Laravel; incorporar los parámetros de `.env.supabase.example`. `config/database.php` ahora lee `DB_SCHEMA` para seleccionar este esquema. El valor por defecto sigue siendo `public`; las conexiones MySQL existentes conservan su configuración.

El host del ejemplo corresponde a la conexión directa. Si el alojamiento necesita IPv4, obtener del panel **Connect** el host exacto del **Session pooler**, usar puerto 5432 y el usuario `teralinde_crm_app.kslzmrddrhfyyrxyfmbw`. Mantener TLS con `DB_SSLMODE=require`.

La aplicación todavía **no está conectada ni desplegada** contra este esquema. No se dispone aquí de PHP/Composer para ejecutar Artisan o sus pruebas. La configuración del driver no sustituye esa comprobación.

## Comprobaciones realizadas

Se probaron operaciones de creación, lectura, actualización y eliminación con el rol de backend; estado inicial; integridad cliente-visita; estados permitidos; correo único; eliminación en cascada; lectura de las seis migraciones y restricciones para crear tablas o escribir el historial. Todas pasaron.

La prueba usa datos ficticios y una transacción con `ROLLBACK`; no quedaron clientes, visitas ni usuarios de prueba. La concesión temporal de `SET ROLE` también se revirtió.

Los asesores de Supabase no reportaron avisos de seguridad para este esquema. Los avisos informativos de índices sin uso son esperables en tablas nuevas y vacías.

## Documentación oficial

- https://supabase.com/docs/guides/database/connecting-to-postgres
- https://supabase.com/docs/guides/database/postgres/roles
- https://laravel.com/docs/12.x/database
