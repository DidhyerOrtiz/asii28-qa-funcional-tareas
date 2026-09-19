# Caso practico arquitectonico

## Enunciado

Un Analista QA del Hospital A ejecuta una campana contra la version `29.1.0`.
Un caso esperaba 403 para un usuario sin permiso, pero la API respondio 200 y
modifico datos. El analista debe registrar el fallo, conservar evidencia saneada,
comunicar una version corregida y re-probar. Un usuario del Hospital B no debe ver
la campana ni el defecto.

## Solucion propuesta

### Actores

- Analista QA: ejecuta y registra resultado.
- Responsable del modulo: corrige y comunica `fixed_version`.
- Revisor: consulta la matriz si posee permiso.
- HIS bajo prueba: produce el comportamiento observado.

### Casos de uso

1. Crear caso trazable con requisito y criterio de aceptacion.
2. Crear campana para `29.1.0`.
3. Registrar ejecucion `FAILED` con evidencia privada.
4. Crear defecto `OPEN` atomicamente.
5. Registrar re-prueba sobre `29.1.1`.
6. Cerrar defecto si el resultado es `PASSED`.
7. Consultar matriz consolidada.

### Flujo por capas

1. La SPA envia JWT, tenant y JSON.
2. Middleware valida usuario, Hospital A y permiso.
3. `RecordExecutionRequest` valida estado, evidencia y campos de fallo.
4. `QaRegressionController` crea un comando.
5. `ManageQaRegression` verifica campana y membresia del caso.
6. Domain construye ejecucion, evidencia y defecto validos.
7. `QaRegressionRepository` registra la operacion.
8. Eloquent confirma tres filas en una transaccion PostgreSQL.
9. La API responde 201 con ejecucion y defecto.

### Reglas clave

- El hospital procede del contexto, no del payload.
- La evidencia usa `private/qa/`, SHA-256 y `redacted=true`.
- `FAILED` exige reproduccion, severidad y version detectada.
- Hospital B recibe no encontrado/prohibido sin conocer datos de A.
- La re-prueba no sobrescribe la ejecucion original.
- `PASSED` cierra; `FAILED`/`BLOCKED` reabre.

### Decisiones justificadas

| Decision | Justificacion |
|---|---|
| Repository | Desacopla aplicacion de persistencia y permite pruebas InMemory. |
| Transaccion local | Evita ejecucion sin evidencia o defecto parcial. |
| Monolito modular | Reutiliza seguridad/tenant y evita distribucion prematura. |
| Referencia privada | Reduce exposicion; no afirma verificar fisicamente el archivo. |
| Filtro tenant en varias capas | Defensa en profundidad frente a acceso cruzado. |

## Respuesta corta para examen

La solucion usa cliente-servidor y cuatro capas. La SPA envia la observacion; la
API autentica, resuelve tenant y autoriza; Application orquesta; Domain valida;
Repository persiste atomicamente. El fallo genera ejecucion, evidencia y defecto
`OPEN`; la re-prueba crea otra ejecucion y cierra o reabre. Todo se filtra por
hospital y permanece local aunque el caso pruebe CENTRAL.
