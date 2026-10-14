# Base independiente de Teralinde CRM

## Estado comprobado el 13 de octubre de 2026 (Peru)

La base propia de Teralinde ya esta creada y verificada en Neon, separada de EcoSphere y de Sistema Municipal.

| Dato | Valor confirmado |
| --- | --- |
| Organizacion | Gabriel, plan Free |
| Proyecto | `teralinde-crm` |
| Project ID | `frosty-sky-96314663` |
| Branch | `main`, `br-twilight-snow-ar3etcyu` |
| Base de datos | `teralinde_crm` |
| Esquema | `teralinde_crm` |
| Motor | Lakebase Postgres, compatible con PostgreSQL 17 |
| Region | AWS US West 2, Oregon |
| Compute | minimo y maximo 0.25 CU |
| Rol operador | `teralinde_crm_owner` |
| Rol del futuro backend | `teralinde_crm_app` |

La creacion y todas las consultas o cambios SQL de esta fase se dirigieron exclusivamente a este proyecto nuevo. No se modificaron bases Supabase ni otros proyectos Neon.

## Regla del propietario

Teralinde tiene su propio proyecto y su propia base. No crear esquemas, tablas, roles o permisos en bases de otros proyectos para reutilizar sus recursos. Usar siempre los IDs explicitos de la tabla anterior; no confiar en el proyecto por defecto de una herramienta.

Lo añadido antes en EcoSphere sigue pendiente de retirada. Su limpieza requiere autorizacion especifica para esa base y no forma parte de esta creacion independiente.

## Estructura aplicada

11 tablas: `migrations`, `users`, `password_reset_tokens`, `sessions`, `cache`, `cache_locks`, `jobs`, `job_batches`, `failed_jobs`, `clientes` y `visitas`.

El esquema reproduce las seis migraciones Laravel existentes y registra sus nombres en `teralinde_crm.migrations`. Los clientes y las visitas se relacionan por clave foranea, con estados permitidos, correo de usuario unico e indices de agenda.

`database/bootstrap/teralinde_postgres.sql` conserva la SQL aplicada mas una comprobacion de destino. Es un bootstrap para una base nueva y vacia; **no volver a ejecutarlo en la base ya inicializada**.

## Permisos

Todas las tablas tienen RLS activado y forzado. PUBLIC no tiene acceso al esquema. El rol de backend puede operar los registros del CRM y consultar las migraciones, pero no crear tablas ni modificar su historial. No hereda privilegios de administracion, no puede crear roles y no omite RLS.

`teralinde_crm_app` permanece NOLOGIN, sin contraseña. El operador del proyecto permite gestionar la base mediante el complemento Neon. Antes de desplegar Laravel, generar una credencial exclusiva del backend y guardarla como secreto del servidor.

Las politicas permiten al backend operar todos los datos del CRM. Los permisos de cada usuario o negocio deben implementarse y verificarse en Laravel antes de abrir el servicio a clientes.

## Verificacion realizada en Neon

Pasaron nueve comprobaciones: operaciones de creacion/lectura/actualizacion/eliminacion, estado inicial, integridad cliente-visita, estados permitidos, correo unico, eliminacion en cascada, lectura del historial y restricciones para crear tablas o escribir migraciones.

La prueba de `database/tests/teralinde_crm.sql` exige tablas de negocio nuevas y vacias. Usa registros ficticios dentro de una subtransaccion revertida. La consulta final confirmo cero usuarios, clientes y visitas; solo quedaron las seis entradas del historial de Laravel.

Estas son pruebas de la base, no de la aplicacion PHP completa.

## Conexion futura de Laravel

`.env.neon.example` incluye el host pooled confirmado por Neon, la base y el esquema; no incluye ninguna contraseña. Conservar `APP_KEY` y los demas valores privados del `.env` completo. `config/database.php` ya lee `DB_SCHEMA`.

El rol tiene `search_path=teralinde_crm` configurado en el servidor. Antes de usar el pooler con PDO/Laravel, comprobar el flujo real y sus ajustes de sesion. Para Artisan, migraciones administrativas o comandos que dependan de estado de sesion, obtener del panel Connect la conexion **directa** del mismo proyecto.

El alojamiento requerira `pdo_pgsql`. No se dispone de PHP/Composer en este entorno; Laravel todavia no esta conectado ni desplegado y no se ejecutaron sus pruebas.

## Referencias oficiales

- https://neon.com/docs/ai/neon-mcp-server
- https://neon.com/docs/manage/databases
- https://neon.com/docs/manage/roles
- https://neon.com/docs/connect/connection-pooling
- https://laravel.com/docs/12.x/database
