# Evidencia local - Semana 5

## 1. Fuentes contrastadas

| Evidencia | Uso |
|---|---|
| `routes/api.php` | Confirmar las cinco rutas y middleware. |
| `QaRegressionController.php` | Confirmar comandos, respuestas y errores. |
| Cuatro `FormRequest` del Modulo 29 | Confirmar campos, limites y condicionales. |
| `ManageQaRegression.php` | Confirmar reglas y orquestacion. |
| `QaRegressionRepository` y adaptadores | Confirmar frontera y transacciones. |
| Migracion `000029` | Confirmar tablas, FKs, unicos e indices. |
| Pruebas Modulo 29 | Confirmar comportamiento observable. |

## 2. Estado funcional de referencia

La implementacion de referencia se encuentra en la rama colaborativa
`feat/mod29-qa-regresion`, PR `#63` hacia `develop`.

Resultados documentados y previamente verificados:

```text
Modulo 29: 21 pruebas, 63 aserciones - PASS
Sin PostgreSQL: 19 pruebas, 50 aserciones - PASS
PostgreSQL focal: 2 pruebas, 13 aserciones - PASS
Modulo 20: 13 pruebas, 30 aserciones - PASS
migrate:fresh --seed en PostgreSQL 17: PASS
```

Estos resultados no se sustituyen por el diagrama. La fuente Mermaid explica la
integracion, mientras las pruebas respaldan el comportamiento.

## 3. Revalidacion local del 17 de septiembre de 2026

```powershell
php --version
php artisan --version
php artisan route:list --path=api/v1/qa
php -d extension=pdo_pgsql vendor/bin/phpunit tests/Unit/Modulo29 tests/Feature/Modulo29 --exclude-group postgres
```

Resultado observado:

```text
PHP 8.3.31
Laravel Framework 12.58.0
5 rutas QA registradas
21 pruebas descubiertas; 19 ejecutadas en PASS, 2 PostgreSQL omitidas, 50 aserciones
```

## 4. Checklist documental

- [x] Cinco endpoints exactos.
- [x] Payloads de caso, campana, ejecucion y re-prueba.
- [x] Respuesta de matriz.
- [x] Errores 401, 403, 404, 409 y 422.
- [x] JWT, tenant y permiso documentados.
- [x] Privacidad de evidencia documentada.
- [x] Issue, rama, worktree y PR planificados.
- [x] Decision de monolito modular justificada.
- [x] Fuente Mermaid editable preparada.
- [ ] PNG exportado manualmente y revisado.
- [ ] Actualizacion manual en Project Odoo.
- [ ] Publicacion Git autorizada por el estudiante.

## 5. Privacidad

Los ejemplos usan UUID, versiones, usuarios y resultados ficticios. No contienen
tokens, contrasenas, cookies, historias clinicas ni datos de pacientes.
