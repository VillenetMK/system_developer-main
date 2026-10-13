# Intento anterior de Supabase: destino descartado

La configuración compartida dentro de EcoSphere queda descartada por instrucción del propietario. Teralinde debe tener un proyecto y una base de datos nuevos e independientes.

El esquema `teralinde_crm` y el rol añadidos previamente en EcoSphere permanecen allí. No se harán más escrituras en esa base sin autorización específica, incluida su retirada.

El archivo `database/bootstrap/teralinde_supabase.sql` conserva el SQL histórico aplicado. `database/tests/teralinde_crm.sql` conserva la prueba histórica de ese esquema. No ejecutarlos sobre bases existentes.

Consultar [Base independiente de Teralinde CRM](TERALINDE_DATABASE.md) para el destino nuevo elegido. `.env.supabase.example` ahora contiene un marcador para un proyecto nuevo; no apunta a EcoSphere.
