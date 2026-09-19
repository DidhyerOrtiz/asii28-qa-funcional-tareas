# Contrato API REST - Modulo 29

## 1. Convenciones generales

| Propiedad | Contrato |
|---|---|
| Base | `/api/v1` |
| Recurso | `/qa` |
| Formato | JSON sobre HTTPS |
| Autenticacion | `Authorization: Bearer <jwt>` |
| Contexto | Cabecera tenant utilizada por el HIS; el servidor obtiene `hospital_id` del contexto resuelto. |
| Autorizacion | Permiso `qa.regression.manage` con guard `api`. |
| Identificadores | UUID para casos, campanas, ejecuciones, evidencias y defectos. |
| Estados de ejecucion | `PASSED`, `FAILED`, `BLOCKED`. |
| Severidad | `LOW`, `MEDIUM`, `HIGH`, `CRITICAL`. |

El cliente no puede enviar `hospital_id`, `created_by` ni `executed_by`. Esos
valores proceden del tenant y usuario autenticados.

## 2. Resumen de endpoints

| Metodo | Ruta | Resultado | Objetivo |
|---|---|---:|---|
| POST | `/api/v1/qa/test-cases` | 201 | Crear caso manual trazable. |
| POST | `/api/v1/qa/campaigns` | 201 | Crear campana y asociar casos. |
| POST | `/api/v1/qa/campaigns/{campaign}/executions` | 201 | Registrar resultado y evidencia; crear defecto si falla. |
| POST | `/api/v1/qa/defects/{defect}/retests` | 201 | Registrar version corregida y re-prueba. |
| GET | `/api/v1/qa/campaigns/{campaign}/matrix` | 200 | Consultar matriz consolidada. |

No se documentan endpoints de editar o eliminar casos/campanas, carga fisica de
archivos, automatizacion E2E ni sincronizacion CENTRAL porque no existen.

## 3. Crear caso de prueba

`POST /api/v1/qa/test-cases`

### Solicitud

```json
{
  "target_scope": "HOSPITAL",
  "requirement_ref": "MOD-02/RF-07",
  "acceptance_criterion_ref": "MOD-02/CA-03",
  "title": "Rechazar operacion sin permiso",
  "preconditions": "Usuario ficticio sin el permiso requerido",
  "steps": [
    "Autenticar al usuario ficticio",
    "Invocar la operacion protegida",
    "Observar la respuesta"
  ],
  "expected_result": "La API responde 403 y no modifica datos"
}
```

| Campo | Regla principal |
|---|---|
| `target_scope` | Obligatorio; `CENTRAL` o `HOSPITAL`. |
| `requirement_ref` | Obligatorio, maximo 255 caracteres. |
| `acceptance_criterion_ref` | Obligatorio, maximo 255 caracteres. |
| `title` | Obligatorio, maximo 255 caracteres. |
| `preconditions` | Opcional, maximo 5000 caracteres. |
| `steps` | Entre 1 y 100 textos; cada uno maximo 2000 caracteres. |
| `expected_result` | Obligatorio, maximo 5000 caracteres. |

### Respuesta 201

```json
{
  "test_case": {
    "id": "<uuid>",
    "hospital_id": "<tenant-uuid>",
    "target_scope": "HOSPITAL",
    "requirement_ref": "MOD-02/RF-07",
    "acceptance_criterion_ref": "MOD-02/CA-03",
    "title": "Rechazar operacion sin permiso",
    "preconditions": "Usuario ficticio sin el permiso requerido",
    "steps": ["..."],
    "expected_result": "La API responde 403 y no modifica datos",
    "created_by": 10
  }
}
```

## 4. Crear campana

`POST /api/v1/qa/campaigns`

### Solicitud

```json
{
  "name": "Regresion permisos 2026-09-17",
  "target_version": "release-2026.09.17",
  "test_case_ids": [
    "018f1f8a-7c4e-7000-8000-000000000001"
  ]
}
```

`test_case_ids` admite entre 1 y 500 UUID distintos. Todos los casos deben
existir en el mismo hospital. La respuesta 201 contiene `campaign`.

## 5. Registrar ejecucion

`POST /api/v1/qa/campaigns/{campaign}/executions`

### Solicitud aprobada o bloqueada

```json
{
  "test_case_id": "018f1f8a-7c4e-7000-8000-000000000001",
  "status": "PASSED",
  "actual_result": "La API respondio 403 y no modifico datos",
  "evidence": {
    "path": "private/qa/campaign-01/execution-01.json",
    "checksum": "0123456789abcdef0123456789abcdef0123456789abcdef0123456789abcdef",
    "metadata": {
      "redacted": true,
      "mime": "application/json",
      "size": 824
    }
  }
}
```

### Campos adicionales para `FAILED`

```json
{
  "reproduction_steps": "1. Autenticar sin permiso. 2. Invocar. 3. Observar 200.",
  "severity": "HIGH",
  "detected_version": "release-2026.09.17"
}
```

Los campos de defecto son planos dentro del mismo payload. Una ejecucion
`FAILED` valida crea atomicamente ejecucion, evidencia y defecto `OPEN`. Una
ejecucion `PASSED` o `BLOCKED` devuelve `defect: null`.

### Respuesta 201 para fallo

```json
{
  "execution": {
    "id": "<uuid>",
    "status": "FAILED",
    "defect_id": "<uuid>",
    "evidence": {"path": "private/qa/campaign-01/execution-01.json"}
  },
  "defect": {
    "id": "<uuid>",
    "status": "OPEN",
    "detected_version": "release-2026.09.17",
    "fixed_version": null
  }
}
```

## 6. Re-probar defecto

`POST /api/v1/qa/defects/{defect}/retests`

### Solicitud

```json
{
  "fixed_version": "release-2026.09.18",
  "status": "PASSED",
  "actual_result": "La API respondio 403 y no modifico datos",
  "evidence": {
    "path": "private/qa/campaign-01/retest-01.json",
    "checksum": "1111111111111111111111111111111111111111111111111111111111111111",
    "metadata": {
      "redacted": true,
      "mime": "application/json",
      "size": 790
    }
  }
}
```

La respuesta 201 contiene `execution` y `defect`. `PASSED` cierra el defecto;
`FAILED` o `BLOCKED` lo deja `REOPENED`. Un defecto cerrado rechaza nuevas
re-pruebas con 409.

## 7. Consultar matriz

`GET /api/v1/qa/campaigns/{campaign}/matrix`

### Respuesta 200 resumida

```json
{
  "campaign": {
    "id": "<uuid>",
    "hospital_id": "<tenant-uuid>",
    "name": "Regresion permisos 2026-09-17",
    "target_version": "release-2026.09.17"
  },
  "rows": [
    {
      "test_case": {"id": "<uuid>", "requirement_ref": "MOD-02/RF-07"},
      "latest_status": "PASSED",
      "executions": [
        {"id": "<uuid>", "status": "FAILED"},
        {"id": "<uuid>", "status": "PASSED"}
      ],
      "defects": [
        {"id": "<uuid>", "status": "CLOSED", "fixed_version": "release-2026.09.18"}
      ]
    }
  ]
}
```

Un caso sin ejecucion tiene `latest_status: null`.

## 8. Evidencia y privacidad

- `path` debe comenzar con `private/qa/` y no es una URL publica.
- `checksum` contiene 64 caracteres hexadecimales con sintaxis SHA-256.
- `metadata.redacted` debe ser `true`.
- `metadata.mime` no puede estar vacio y `metadata.size` es entero no negativo.
- Se rechazan recursivamente las claves `password`, `token`, `authorization`,
  `cookie` y `secret` sin distinguir mayusculas.
- El MVP registra la referencia; no abre el archivo ni recalcula el checksum.

## 9. Errores observables

| HTTP | Condicion | Forma |
|---:|---|---|
| 401 | JWT ausente, vencido o invalido. | Respuesta del middleware. |
| 403 | Falta permiso o el contexto tenant no corresponde. | `{"message":"..."}` para errores traducidos. |
| 404 | Recurso no visible dentro del hospital. | `{"message":"..."}`. |
| 409 | Duplicado, enlace inconsistente o defecto cerrado. | `{"message":"..."}`. |
| 422 | Validacion HTTP o de dominio. | Errores Laravel o `{"message":"..."}`. |

El controlador no define una envoltura propia con `code` o `correlation_id`.
