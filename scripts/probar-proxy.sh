#!/bin/sh
#
# Script para probar la resiliencia del proxy NGINX y el backend.
# Realiza 20 peticiones consecutivas al endpoint de health y muestra estadísticas.

if ! command -v curl >/dev/null 2>&1; then
    printf "Error: 'curl' no está instalado. Por favor instálalo para usar este script.\n" >&2
    exit 2
fi

# Parámetro opcional: URL base (por defecto http://localhost)
URL="${1:-http://localhost}"

# Quitar barra final si la tiene usando sed (POSIX)
URL="$(echo "$URL" | sed 's:/*$::')"

printf "Probando proxy en %s/health\n" "$URL"
printf "========================================\n"

i=1
EXITOSAS=0
FALLIDAS=0
TIEMPO_TOTAL=0

while [ "$i" -le 20 ]; do
    RESULT=$(curl -s -o /dev/null -w "%{http_code} %{time_total}" --connect-timeout 3 --max-time 10 "$URL/health" 2>/dev/null)
    
    if [ -z "$RESULT" ]; then
        RESULT="000 0.000"
    fi

    HTTP_CODE=$(echo "$RESULT" | awk '{print $1}')
    TIEMPO=$(echo "$RESULT" | awk '{print $2}')

    if [ "$HTTP_CODE" = "200" ]; then
        printf "Petición %2d: OK   - Código %s - Tiempo: %s s\n" "$i" "$HTTP_CODE" "$TIEMPO"
        EXITOSAS=$((EXITOSAS + 1))
    else
        printf "Petición %2d: FAIL - Código %s - Tiempo: %s s\n" "$i" "$HTTP_CODE" "$TIEMPO"
        FALLIDAS=$((FALLIDAS + 1))
    fi

    TIEMPO_TOTAL=$(awk "BEGIN {print $TIEMPO_TOTAL + $TIEMPO}")

    i=$((i + 1))
done

printf "========================================\n"
printf "Resumen:\n"
printf "Total peticiones: 20\n"
printf "Exitosas (200)  : %d\n" "$EXITOSAS"
printf "Fallidas (!200) : %d\n" "$FALLIDAS"

TIEMPO_PROMEDIO=$(awk "BEGIN {printf \"%.3f\", $TIEMPO_TOTAL / 20}")
printf "Tiempo promedio : %s s\n" "$TIEMPO_PROMEDIO"

if [ "$FALLIDAS" -gt 0 ]; then
    exit 1
fi

exit 0
