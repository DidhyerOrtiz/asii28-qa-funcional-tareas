# ASII-28 - Semana 11

**Entrega:** Prototipo navegable desktop/mobile

## Objetivo

Materializar los componentes, flujo UX, mejoras de accesibilidad y reglas
responsive de semanas 7 a 10 en un prototipo autocontenido.

## Ejecutar

Abrir [`prototype/index.html`](prototype/index.html) en un navegador moderno. No
requiere instalacion, servidor, dependencias ni credenciales.

## Vistas incluidas

- Resumen de campana.
- Listado de campanas.
- Espacio de ejecucion manual.
- Cola de defectos.

## Interacciones

- Navegacion entre vistas en escritorio y movil.
- Filtro visual de campanas.
- Seleccion de estado de ejecucion.
- Campos condicionales para `FAILED`.
- Confirmacion local mediante region `aria-live`.

## Limites

El prototipo usa datos ficticios y no consume Laravel. No autentica, persiste,
sube archivos ni verifica checksums. La futura implementacion Vue debe conservar
el contrato REST y las reglas de seguridad documentadas.

## Evidencia sugerida

Tomar capturas manuales en 1440 px y 390 px despues de revisar el prototipo. No
incluir datos reales en las capturas.
