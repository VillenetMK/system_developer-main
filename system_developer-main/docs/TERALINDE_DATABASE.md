# Base independiente de Teralinde CRM

## Regla del propietario

Crear un proyecto y una base de datos propios para Teralinde. No escribir en EcoSphere, Sistema Municipal ni ninguna otra base existente. Los permisos técnicos disponibles no sustituyen esta restricción.

## Destino elegido

Neon, PostgreSQL, proyecto nuevo `teralinde-crm`, base `teralinde_crm`, plan Free. La creación está pendiente de conectar el complemento Neon; todavía no hay un proyecto, host ni credenciales confirmados. Se comprobará la cuenta y el plan antes de crear el recurso.

El complemento permite crear proyectos y ejecutar SQL. La elección se apoya en la documentación oficial:
- https://neon.com/docs/ai/neon-mcp-server
- https://neon.com/blog/neon-free-plan-1-gb-per-project

## Preparación del código

Laravel sigue utilizando sus migraciones y modelos existentes. La conexión PostgreSQL lee `DB_SCHEMA`; su valor por defecto es `public`. `.env.neon.example` contiene solamente parámetros de base para combinar con el `.env` completo del servidor, no sustituye `APP_KEY` ni los demás ajustes.

Los valores HOST, usuario y contraseña se completarán exclusivamente con la conexión del proyecto nuevo. No copiar los de otros sistemas. El servidor requerirá `pdo_pgsql`.

`database/bootstrap/teralinde_postgres.sql` prepara las mismas 11 tablas, las seis migraciones originales y un rol de ejecución separado. Es un script inicial para una base nueva y vacía, **todavía no aplicado en Neon**. Debe revisarse contra los permisos reales de la cuenta durante el aprovisionamiento.

## Corrección del intento anterior

Antes de esta regla se añadió el esquema `teralinde_crm` y el rol `teralinde_crm_app` dentro de EcoSphere. Ese despliegue compartido se descarta como destino de Teralinde. El esquema y el rol aún permanecen allí: retirarlos requiere autorización expresa para escribir en esa base.

No se está borrando, reutilizando ni consultando información comercial de las bases existentes. La configuración del repositorio ya no contiene un host de EcoSphere.

Las pruebas SQL anteriores corresponden al esquema de Supabase, no a Neon. La base nueva requerirá comprobar creación, consulta, edición, integridad y permisos cuando se conecte el complemento. No hay un backend Laravel desplegado.
