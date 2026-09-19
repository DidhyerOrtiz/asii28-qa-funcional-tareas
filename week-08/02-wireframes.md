# Wireframes iniciales

## 1. Resumen desktop

```text
+----------------+------------------------------------------------+
| QA 29          | Hospital Norte     Usuario QA        Salir      |
| Resumen        +------------------------------------------------+
| Campanas       | Regresion 29.1                         En curso |
| Ejecucion      | [72% cobertura] [3 fallos] [1 bloqueado]       |
| Defectos       +------------------------------------------------+
|                | Siguiente caso                                 |
|                | REQ-29-14 - Aislamiento tenant                 |
|                | [Continuar ejecucion]                          |
|                +------------------------------------------------+
|                | Actividad reciente / defectos prioritarios     |
+----------------+------------------------------------------------+
```

## 2. Espacio de ejecucion desktop

```text
+---------------------------------------------------------------+
| Campana / Caso 4 de 12                          Guardado 10:42 |
+----------------------------+----------------------------------+
| Caso                       | Registrar resultado              |
| Precondiciones             | ( ) PASSED ( ) FAILED ( ) BLOCKED|
| 1. Autenticar              | Resultado observado             |
| 2. Cambiar tenant          | [..............................] |
| Resultado esperado         | Evidencia privada               |
| Acceso rechazado           | [ruta] [checksum]               |
|                            | [Guardar y siguiente]           |
+----------------------------+----------------------------------+
```

## 3. Movil

```text
+------------------------------+
| QA 29     Hospital Norte  [ ] |
| Regresion 29.1   Caso 4/12    |
|------------------------------|
| REQ-29-14                    v|
| Precondiciones y pasos        |
| [1] Autenticar                |
| [2] Cambiar tenant            |
|------------------------------|
| Resultado                     |
| [PASSED] [FAILED] [BLOCKED]   |
| [Resultado observado.......]  |
| [Agregar evidencia]           |
| [Guardar resultado]           |
|------------------------------|
| Inicio Campanas Ejecutar Bugs |
+------------------------------+
```

En movil el contenido se apila y la accion primaria permanece accesible sin
ocultar la identidad de campana/caso.
