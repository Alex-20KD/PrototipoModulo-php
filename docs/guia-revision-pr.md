# Guía de Revisión de Pull Requests (Code Review)

Esta guía establece los principios y el orden recomendado para revisar los Pull Requests en el proyecto **MedTriaje**. El objetivo principal de la revisión es cuidar la calidad del código, evitar regresiones y fomentar el aprendizaje mutuo.

---

## 👥 Rotación de Roles

- **La revisión es compartida y rotativa:** Todos los integrantes del equipo revisan y son revisados. Nadie aprueba sus propios cambios.
- No esperes a que una sola persona revise todo; si ves un PR abierto asignado o pendiente, participa activamente.

---

## 🔍 Orden Recomendado de Revisión

Sigue este orden secuencial para hacer una revisión estructurada y eficiente:

### 1. ¿Resuelve el problema o cumple el issue?
- Lee la descripción del PR y el issue vinculado (`Closes #...`).
- Verifica si la solución propuesta responde directamente a los criterios de aceptación sin añadir complejidad innecesaria.

### 2. ¿Tiene pruebas suficientes y pasan?
- Confirma que el PR incluya pruebas unitarias o de integración para la lógica nueva o modificada.
- Verifica que el CI esté en verde (`lint` y `tests`).
- Pregúntate: *¿Qué casos extremos o fallos comunes podrían romper este código? ¿Están cubiertos?*

### 3. ¿Presenta riesgos de seguridad o concurrencia?
- **Seguridad:**
  - ¿Se validan todas las entradas del usuario (`FormRequest` o `$request->validate`)?
  - ¿Hay riesgo de inyección SQL o exposición de datos sensibles?
  - ¿Se subió accidentalmente algún secreto o dato hardcodeado?
- **Concurrencia y Base de Datos:**
  - Dado que operamos dos instancias de backend (`app` y `app2`), ¿hay operaciones propensas a condiciones de carrera (race conditions)?
  - Si hay migraciones, ¿son seguras y no destructivas?

### 4. ¿El código es claro, mantenible y respeta el estilo?
- ¿Los nombres de variables, métodos y clases son descriptivos y siguen las convenciones del proyecto?
- ¿El flujo es fácil de entender sin necesidad de comentarios excesivos?
- ¿El formateador Pint pasó sin problemas de estilo?

---

## 💬 Comunicación y Convenciones de Comentarios

- **Tono respetuoso y constructivo:** Critica el código, nunca a la persona. Explica el *porqué* detrás de una sugerencia en lugar de solo señalar el error.
- **Observaciones menores (`nit:`):**
  - Usa el prefijo `nit:` (de *nitpick*) para sugerencias cosméticas, preferencias personales o mejoras mínimas que **no bloquean la aprobación** del PR.
  - *Ejemplo:* `nit: podrías extraer esta expresión a una variable para mayor legibilidad.`
- **Aprobación ágil:** Si solo quedan comentarios marcados con `nit:`, aprueba el PR permitiendo que el autor decida si los aplica antes de fusionar.
