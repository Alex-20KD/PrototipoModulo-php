## 📝 Descripción
<!-- Resume qué cambia este PR y por qué es necesario. -->

## 🔗 Issue Relacionado
<!-- Vincula el issue correspondiente para cerrarlo al fusionar, p. ej. Closes #123 -->
Closes #

## 🧪 Cómo Probarlo
<!-- Detalla los pasos para reproducir o verificar este cambio localmente -->
1. 
2. 
3. 

## ✅ Checklist de Calidad
Antes de solicitar revisión, confirma que cumples con lo siguiente:
- [ ] Ejecuté Pint y el formato está limpio (`docker compose exec app vendor/bin/pint`).
- [ ] Ejecuté las pruebas locales y pasaron al 100% (`docker compose exec app php artisan test`).
- [ ] No incluí credenciales, secretos ni archivos `.env`.
- [ ] Si incluye migraciones, avisé previamente al equipo y no modifiqué migraciones ya mergeadas.
- [ ] La documentación fue actualizada si el cambio lo requería.
- [ ] El PR es conciso (idealmente menos de 400 líneas).
