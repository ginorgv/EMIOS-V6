//
// Funciones para el dibujado de "elementos" de tarifas
//


// Devueleve las características del tipo de tarifa eléctrica de España
function dame_caracteristicas_tipo_tarifa_electrica_Espanya(tipo_tarifa) {
    var caracteristicas_tipo_tarifa = {};
    switch (tipo_tarifa) {
        // Tipos de tarifas eléctricas (vigentes a partir de 2020)
        case TIPO_TARIFA_ELECTRICA_ESPANYA_20TD_P:
        case TIPO_TARIFA_ELECTRICA_ESPANYA_20TD_B:
        case TIPO_TARIFA_ELECTRICA_ESPANYA_20TD_C:
        case TIPO_TARIFA_ELECTRICA_ESPANYA_20TD_CE:
        case TIPO_TARIFA_ELECTRICA_ESPANYA_20TD_ME:
        case TIPO_TARIFA_ELECTRICA_ESPANYA_20TD_P_2022:
        case TIPO_TARIFA_ELECTRICA_ESPANYA_20TD_B_2022:
        case TIPO_TARIFA_ELECTRICA_ESPANYA_20TD_C_2022:
        case TIPO_TARIFA_ELECTRICA_ESPANYA_20TD_CE_2022:
        case TIPO_TARIFA_ELECTRICA_ESPANYA_20TD_ME_2022:
        case TIPO_TARIFA_ELECTRICA_ESPANYA_20TD_P_2023:
        case TIPO_TARIFA_ELECTRICA_ESPANYA_20TD_B_2023:
        case TIPO_TARIFA_ELECTRICA_ESPANYA_20TD_C_2023:
        case TIPO_TARIFA_ELECTRICA_ESPANYA_20TD_CE_2023:
        case TIPO_TARIFA_ELECTRICA_ESPANYA_20TD_ME_2023:
        case TIPO_TARIFA_ELECTRICA_ESPANYA_20TD_P_2024:
        case TIPO_TARIFA_ELECTRICA_ESPANYA_20TD_B_2024:
        case TIPO_TARIFA_ELECTRICA_ESPANYA_20TD_C_2024:
        case TIPO_TARIFA_ELECTRICA_ESPANYA_20TD_CE_2024:
        case TIPO_TARIFA_ELECTRICA_ESPANYA_20TD_ME_2024:
        case TIPO_TARIFA_ELECTRICA_ESPANYA_20TD_P_2025:
        case TIPO_TARIFA_ELECTRICA_ESPANYA_20TD_B_2025:
        case TIPO_TARIFA_ELECTRICA_ESPANYA_20TD_C_2025:
        case TIPO_TARIFA_ELECTRICA_ESPANYA_20TD_CE_2025:
        case TIPO_TARIFA_ELECTRICA_ESPANYA_20TD_ME_2025:
        case TIPO_TARIFA_ELECTRICA_ESPANYA_20TD_P_2025_ABRIL:
        case TIPO_TARIFA_ELECTRICA_ESPANYA_20TD_B_2025_ABRIL:
        case TIPO_TARIFA_ELECTRICA_ESPANYA_20TD_C_2025_ABRIL:
        case TIPO_TARIFA_ELECTRICA_ESPANYA_20TD_CE_2025_ABRIL:
        case TIPO_TARIFA_ELECTRICA_ESPANYA_20TD_ME_2025_ABRIL:
        case TIPO_TARIFA_ELECTRICA_ESPANYA_20TD_P_2026:
        case TIPO_TARIFA_ELECTRICA_ESPANYA_20TD_B_2026:
        case TIPO_TARIFA_ELECTRICA_ESPANYA_20TD_C_2026:
        case TIPO_TARIFA_ELECTRICA_ESPANYA_20TD_CE_2026:
        case TIPO_TARIFA_ELECTRICA_ESPANYA_20TD_ME_2026:
        {
            caracteristicas_tipo_tarifa["numero_tramos"] = 3;
            caracteristicas_tipo_tarifa["tramos_potencias_iguales"] = [[1, 2], [3]];
            caracteristicas_tipo_tarifa["tipo_calculo_coste_potencias"] = TIPO_CALCULO_COSTE_POTENCIAS_SIN_EXCESOS;
            caracteristicas_tipo_tarifa["excesos_energia_reactiva"] = false;
            caracteristicas_tipo_tarifa["tramos_penalizables_energia_reactiva"] = null;
            caracteristicas_tipo_tarifa["parametros_medida_datos_facturacion"] = false;
            caracteristicas_tipo_tarifa["potencia_maxima_tramos"] = POTENCIA_MAXIMA_TARIFA_ELECTRICA_ESPANYA_2_TRAMOS;
            caracteristicas_tipo_tarifa["potencias_tramos_crecientes"] = false;
            caracteristicas_tipo_tarifa["potencia_minima_algun_tramo"] = null;
            break;
        }
        case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_P:
        case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_B:
        case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_C:
        case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_CE:
        case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_ME:
        case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_P_2022:
        case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_B_2022:
        case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_C_2022:
        case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_CE_2022:
        case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_ME_2022:
        case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_P_2023:
        case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_B_2023:
        case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_C_2023:
        case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_CE_2023:
        case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_ME_2023:
        case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_P_2024:
        case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_B_2024:
        case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_C_2024:
        case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_CE_2024:
        case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_ME_2024:
        case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_P_2025:
        case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_B_2025:
        case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_C_2025:
        case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_CE_2025:
        case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_ME_2025:
        case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_P_2025_ABRIL:
        case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_B_2025_ABRIL:
        case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_C_2025_ABRIL:
        case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_CE_2025_ABRIL:
        case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_ME_2025_ABRIL:
        case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_P_2026:
        case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_B_2026:
        case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_C_2026:
        case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_CE_2026:
        case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_ME_2026:
        {
            caracteristicas_tipo_tarifa["numero_tramos"] = 6;
            caracteristicas_tipo_tarifa["tramos_potencias_iguales"] = null;
            caracteristicas_tipo_tarifa["tipo_calculo_coste_potencias"] = TIPO_CALCULO_COSTE_POTENCIAS_EXCESOS_CUARTOHORARIOS;
            caracteristicas_tipo_tarifa["excesos_energia_reactiva"] = true;
            caracteristicas_tipo_tarifa["tramos_penalizables_energia_reactiva"] = [1, 2, 3, 4, 5];
            caracteristicas_tipo_tarifa["parametros_medida_datos_facturacion"] = false;
            caracteristicas_tipo_tarifa["potencia_maxima_tramos"] = null;
            caracteristicas_tipo_tarifa["potencias_tramos_crecientes"] = true;
            caracteristicas_tipo_tarifa["potencia_minima_algun_tramo"] = POTENCIA_MINIMA_TARIFA_ELECTRICA_ESPANYA_3_TRAMOS_0_ALGUN_TRAMO + INCREMENTO_POTENCIA_MINIMA_ALGUN_TRAMO;
            break;
        }
        case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_P_MAX:
        case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_B_MAX:
        case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_C_MAX:
        case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_CE_MAX:
        case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_ME_MAX:
        case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_P_2022_MAX:
        case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_B_2022_MAX:
        case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_C_2022_MAX:
        case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_CE_2022_MAX:
        case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_ME_2022_MAX:
        case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_P_2023_MAX:
        case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_B_2023_MAX:
        case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_C_2023_MAX:
        case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_CE_2023_MAX:
        case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_ME_2023_MAX:
        case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_P_2024_MAX:
        case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_B_2024_MAX:
        case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_C_2024_MAX:
        case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_CE_2024_MAX:
        case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_ME_2024_MAX: 
        case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_P_2025_MAX:
        case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_B_2025_MAX:
        case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_C_2025_MAX:
        case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_CE_2025_MAX:
        case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_ME_2025_MAX:
        case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_P_2025_ABRIL_MAX:
        case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_B_2025_ABRIL_MAX:
        case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_C_2025_ABRIL_MAX:
        case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_CE_2025_ABRIL_MAX:
        case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_ME_2025_ABRIL_MAX:
        case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_P_2026_MAX:
        case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_B_2026_MAX:
        case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_C_2026_MAX:
        case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_CE_2026_MAX:
        case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_ME_2026_MAX:
        {
            caracteristicas_tipo_tarifa["numero_tramos"] = 6;
            caracteristicas_tipo_tarifa["tramos_potencias_iguales"] = null;
            caracteristicas_tipo_tarifa["tipo_calculo_coste_potencias"] = TIPO_CALCULO_COSTE_POTENCIAS_EXCESOS_MAXIMETRO;
            caracteristicas_tipo_tarifa["excesos_energia_reactiva"] = true;
            caracteristicas_tipo_tarifa["tramos_penalizables_energia_reactiva"] = [1, 2, 3, 4, 5];
            caracteristicas_tipo_tarifa["parametros_medida_datos_facturacion"] = false;
            caracteristicas_tipo_tarifa["potencia_maxima_tramos"] = null;
            caracteristicas_tipo_tarifa["potencias_tramos_crecientes"] = true;
            caracteristicas_tipo_tarifa["potencia_minima_algun_tramo"] = POTENCIA_MINIMA_TARIFA_ELECTRICA_ESPANYA_3_TRAMOS_0_ALGUN_TRAMO + INCREMENTO_POTENCIA_MINIMA_ALGUN_TRAMO;
            break;
        }
        case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_P_MAX:
        case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_B_MAX:
        case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_C_MAX:
        case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_CE_MAX:
        case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_ME_MAX:
        case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_P_2022_MAX:
        case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_B_2022_MAX:
        case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_C_2022_MAX:
        case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_CE_2022_MAX:
        case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_ME_2022_MAX:
        case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_P_2023_MAX:
        case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_B_2023_MAX:
        case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_C_2023_MAX:
        case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_CE_2023_MAX:
        case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_ME_2023_MAX:
        case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_P_2024_MAX:
        case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_B_2024_MAX:
        case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_C_2024_MAX:
        case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_CE_2024_MAX:
        case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_ME_2024_MAX:
        case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_P_2025_MAX:
        case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_B_2025_MAX:
        case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_C_2025_MAX:
        case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_CE_2025_MAX:
        case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_ME_2025_MAX:
        case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_P_2025_ABRIL_MAX:
        case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_B_2025_ABRIL_MAX:
        case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_C_2025_ABRIL_MAX:
        case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_CE_2025_ABRIL_MAX:
        case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_ME_2025_ABRIL_MAX:
        case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_P_2026_MAX:
        case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_B_2026_MAX:
        case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_C_2026_MAX:
        case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_CE_2026_MAX:
        case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_ME_2026_MAX:
        {
            caracteristicas_tipo_tarifa["numero_tramos"] = 6;
            caracteristicas_tipo_tarifa["tramos_potencias_iguales"] = null;
            caracteristicas_tipo_tarifa["tipo_calculo_coste_potencias"] = TIPO_CALCULO_COSTE_POTENCIAS_EXCESOS_MAXIMETRO;
            caracteristicas_tipo_tarifa["excesos_energia_reactiva"] = true;
            caracteristicas_tipo_tarifa["tramos_penalizables_energia_reactiva"] = [1, 2, 3, 4, 5];
            caracteristicas_tipo_tarifa["parametros_medida_datos_facturacion"] = true;
            caracteristicas_tipo_tarifa["potencia_maxima_tramos"] = null;
            caracteristicas_tipo_tarifa["potencias_tramos_crecientes"] = true;
            caracteristicas_tipo_tarifa["potencia_minima_algun_tramo"] = null;
            break;
        }
        case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_P:
        case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_B:
        case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_C:
        case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_CE:
        case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_ME:
        case TIPO_TARIFA_ELECTRICA_ESPANYA_62TD_P:
        case TIPO_TARIFA_ELECTRICA_ESPANYA_62TD_B:
        case TIPO_TARIFA_ELECTRICA_ESPANYA_62TD_C:
        case TIPO_TARIFA_ELECTRICA_ESPANYA_62TD_CE:
        case TIPO_TARIFA_ELECTRICA_ESPANYA_62TD_ME:
        case TIPO_TARIFA_ELECTRICA_ESPANYA_63TD_P:
        case TIPO_TARIFA_ELECTRICA_ESPANYA_63TD_B:
        case TIPO_TARIFA_ELECTRICA_ESPANYA_63TD_C:
        case TIPO_TARIFA_ELECTRICA_ESPANYA_63TD_CE:
        case TIPO_TARIFA_ELECTRICA_ESPANYA_63TD_ME:
        case TIPO_TARIFA_ELECTRICA_ESPANYA_64TD_P:
        case TIPO_TARIFA_ELECTRICA_ESPANYA_64TD_B:
        case TIPO_TARIFA_ELECTRICA_ESPANYA_64TD_C:
        case TIPO_TARIFA_ELECTRICA_ESPANYA_64TD_CE:
        case TIPO_TARIFA_ELECTRICA_ESPANYA_64TD_ME:
        case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_P_2022:
        case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_B_2022:
        case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_C_2022:
        case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_CE_2022:
        case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_ME_2022:
        case TIPO_TARIFA_ELECTRICA_ESPANYA_62TD_P_2022:
        case TIPO_TARIFA_ELECTRICA_ESPANYA_62TD_B_2022:
        case TIPO_TARIFA_ELECTRICA_ESPANYA_62TD_C_2022:
        case TIPO_TARIFA_ELECTRICA_ESPANYA_62TD_CE_2022:
        case TIPO_TARIFA_ELECTRICA_ESPANYA_62TD_ME_2022:
        case TIPO_TARIFA_ELECTRICA_ESPANYA_63TD_P_2022:
        case TIPO_TARIFA_ELECTRICA_ESPANYA_63TD_B_2022:
        case TIPO_TARIFA_ELECTRICA_ESPANYA_63TD_C_2022:
        case TIPO_TARIFA_ELECTRICA_ESPANYA_63TD_CE_2022:
        case TIPO_TARIFA_ELECTRICA_ESPANYA_63TD_ME_2022:
        case TIPO_TARIFA_ELECTRICA_ESPANYA_64TD_P_2022:
        case TIPO_TARIFA_ELECTRICA_ESPANYA_64TD_B_2022:
        case TIPO_TARIFA_ELECTRICA_ESPANYA_64TD_C_2022:
        case TIPO_TARIFA_ELECTRICA_ESPANYA_64TD_CE_2022:
        case TIPO_TARIFA_ELECTRICA_ESPANYA_64TD_ME_2022:
        case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_P_2023:
        case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_B_2023:
        case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_C_2023:
        case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_CE_2023:
        case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_ME_2023:
        case TIPO_TARIFA_ELECTRICA_ESPANYA_62TD_P_2023:
        case TIPO_TARIFA_ELECTRICA_ESPANYA_62TD_B_2023:
        case TIPO_TARIFA_ELECTRICA_ESPANYA_62TD_C_2023:
        case TIPO_TARIFA_ELECTRICA_ESPANYA_62TD_CE_2023:
        case TIPO_TARIFA_ELECTRICA_ESPANYA_62TD_ME_2023:
        case TIPO_TARIFA_ELECTRICA_ESPANYA_63TD_P_2023:
        case TIPO_TARIFA_ELECTRICA_ESPANYA_63TD_B_2023:
        case TIPO_TARIFA_ELECTRICA_ESPANYA_63TD_C_2023:
        case TIPO_TARIFA_ELECTRICA_ESPANYA_63TD_CE_2023:
        case TIPO_TARIFA_ELECTRICA_ESPANYA_63TD_ME_2023:
        case TIPO_TARIFA_ELECTRICA_ESPANYA_64TD_P_2023:
        case TIPO_TARIFA_ELECTRICA_ESPANYA_64TD_B_2023:
        case TIPO_TARIFA_ELECTRICA_ESPANYA_64TD_C_2023:
        case TIPO_TARIFA_ELECTRICA_ESPANYA_64TD_CE_2023:
        case TIPO_TARIFA_ELECTRICA_ESPANYA_64TD_ME_2023:
        case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_P_2024:
        case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_B_2024:
        case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_C_2024:
        case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_CE_2024:
        case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_ME_2024:
        case TIPO_TARIFA_ELECTRICA_ESPANYA_62TD_P_2024:
        case TIPO_TARIFA_ELECTRICA_ESPANYA_62TD_B_2024:
        case TIPO_TARIFA_ELECTRICA_ESPANYA_62TD_C_2024:
        case TIPO_TARIFA_ELECTRICA_ESPANYA_62TD_CE_2024:
        case TIPO_TARIFA_ELECTRICA_ESPANYA_62TD_ME_2024:
        case TIPO_TARIFA_ELECTRICA_ESPANYA_63TD_P_2024:
        case TIPO_TARIFA_ELECTRICA_ESPANYA_63TD_B_2024:
        case TIPO_TARIFA_ELECTRICA_ESPANYA_63TD_C_2024:
        case TIPO_TARIFA_ELECTRICA_ESPANYA_63TD_CE_2024:
        case TIPO_TARIFA_ELECTRICA_ESPANYA_63TD_ME_2024:
        case TIPO_TARIFA_ELECTRICA_ESPANYA_64TD_P_2024:
        case TIPO_TARIFA_ELECTRICA_ESPANYA_64TD_B_2024:
        case TIPO_TARIFA_ELECTRICA_ESPANYA_64TD_C_2024:
        case TIPO_TARIFA_ELECTRICA_ESPANYA_64TD_CE_2024:
        case TIPO_TARIFA_ELECTRICA_ESPANYA_64TD_ME_2024:
        case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_P_2025:
        case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_B_2025:
        case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_C_2025:
        case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_CE_2025:
        case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_ME_2025:
        case TIPO_TARIFA_ELECTRICA_ESPANYA_62TD_P_2025:
        case TIPO_TARIFA_ELECTRICA_ESPANYA_62TD_B_2025:
        case TIPO_TARIFA_ELECTRICA_ESPANYA_62TD_C_2025:
        case TIPO_TARIFA_ELECTRICA_ESPANYA_62TD_CE_2025:
        case TIPO_TARIFA_ELECTRICA_ESPANYA_62TD_ME_2025:
        case TIPO_TARIFA_ELECTRICA_ESPANYA_63TD_P_2025:
        case TIPO_TARIFA_ELECTRICA_ESPANYA_63TD_B_2025:
        case TIPO_TARIFA_ELECTRICA_ESPANYA_63TD_C_2025:
        case TIPO_TARIFA_ELECTRICA_ESPANYA_63TD_CE_2025:
        case TIPO_TARIFA_ELECTRICA_ESPANYA_63TD_ME_2025:
        case TIPO_TARIFA_ELECTRICA_ESPANYA_64TD_P_2025:
        case TIPO_TARIFA_ELECTRICA_ESPANYA_64TD_B_2025:
        case TIPO_TARIFA_ELECTRICA_ESPANYA_64TD_C_2025:
        case TIPO_TARIFA_ELECTRICA_ESPANYA_64TD_CE_2025:
        case TIPO_TARIFA_ELECTRICA_ESPANYA_64TD_ME_2025:
        case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_P_2025_ABRIL:
        case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_B_2025_ABRIL:
        case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_C_2025_ABRIL:
        case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_CE_2025_ABRIL:
        case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_ME_2025_ABRIL:
        case TIPO_TARIFA_ELECTRICA_ESPANYA_62TD_P_2025_ABRIL:
        case TIPO_TARIFA_ELECTRICA_ESPANYA_62TD_B_2025_ABRIL:
        case TIPO_TARIFA_ELECTRICA_ESPANYA_62TD_C_2025_ABRIL:
        case TIPO_TARIFA_ELECTRICA_ESPANYA_62TD_CE_2025_ABRIL:
        case TIPO_TARIFA_ELECTRICA_ESPANYA_62TD_ME_2025_ABRIL:
        case TIPO_TARIFA_ELECTRICA_ESPANYA_63TD_P_2025_ABRIL:
        case TIPO_TARIFA_ELECTRICA_ESPANYA_63TD_B_2025_ABRIL:
        case TIPO_TARIFA_ELECTRICA_ESPANYA_63TD_C_2025_ABRIL:
        case TIPO_TARIFA_ELECTRICA_ESPANYA_63TD_CE_2025_ABRIL:
        case TIPO_TARIFA_ELECTRICA_ESPANYA_63TD_ME_2025_ABRIL:
        case TIPO_TARIFA_ELECTRICA_ESPANYA_64TD_P_2025_ABRIL:
        case TIPO_TARIFA_ELECTRICA_ESPANYA_64TD_B_2025_ABRIL:
        case TIPO_TARIFA_ELECTRICA_ESPANYA_64TD_C_2025_ABRIL:
        case TIPO_TARIFA_ELECTRICA_ESPANYA_64TD_CE_2025_ABRIL:
        case TIPO_TARIFA_ELECTRICA_ESPANYA_64TD_ME_2025_ABRIL:
        case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_P_2026:
        case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_B_2026:
        case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_C_2026:
        case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_CE_2026:
        case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_ME_2026:
        case TIPO_TARIFA_ELECTRICA_ESPANYA_62TD_P_2026:
        case TIPO_TARIFA_ELECTRICA_ESPANYA_62TD_B_2026:
        case TIPO_TARIFA_ELECTRICA_ESPANYA_62TD_C_2026:
        case TIPO_TARIFA_ELECTRICA_ESPANYA_62TD_CE_2026:
        case TIPO_TARIFA_ELECTRICA_ESPANYA_62TD_ME_2026:
        case TIPO_TARIFA_ELECTRICA_ESPANYA_63TD_P_2026:
        case TIPO_TARIFA_ELECTRICA_ESPANYA_63TD_B_2026:
        case TIPO_TARIFA_ELECTRICA_ESPANYA_63TD_C_2026:
        case TIPO_TARIFA_ELECTRICA_ESPANYA_63TD_CE_2026:
        case TIPO_TARIFA_ELECTRICA_ESPANYA_63TD_ME_2026:
        case TIPO_TARIFA_ELECTRICA_ESPANYA_64TD_P_2026:
        case TIPO_TARIFA_ELECTRICA_ESPANYA_64TD_B_2026:
        case TIPO_TARIFA_ELECTRICA_ESPANYA_64TD_C_2026:
        case TIPO_TARIFA_ELECTRICA_ESPANYA_64TD_CE_2026:
        case TIPO_TARIFA_ELECTRICA_ESPANYA_64TD_ME_2026:
        {
            caracteristicas_tipo_tarifa["numero_tramos"] = 6;
            caracteristicas_tipo_tarifa["tramos_potencias_iguales"] = null;
            caracteristicas_tipo_tarifa["tipo_calculo_coste_potencias"] = TIPO_CALCULO_COSTE_POTENCIAS_EXCESOS_CUARTOHORARIOS;
            caracteristicas_tipo_tarifa["excesos_energia_reactiva"] = true;
            caracteristicas_tipo_tarifa["tramos_penalizables_energia_reactiva"] = [1, 2, 3, 4, 5];
            caracteristicas_tipo_tarifa["parametros_medida_datos_facturacion"] = true;
            caracteristicas_tipo_tarifa["potencia_maxima_tramos"] = null;
            caracteristicas_tipo_tarifa["potencias_tramos_crecientes"] = true;
            caracteristicas_tipo_tarifa["potencia_minima_algun_tramo"] = null;
            break;
        }

        // Tipos de tarifas eléctricas (obsoletas a partir de 2020)
        case TIPO_TARIFA_ELECTRICA_ESPANYA_20DHA_P:
        case TIPO_TARIFA_ELECTRICA_ESPANYA_20DHA_B:
        case TIPO_TARIFA_ELECTRICA_ESPANYA_20DHA_C:
        case TIPO_TARIFA_ELECTRICA_ESPANYA_20DHA_CE:
        case TIPO_TARIFA_ELECTRICA_ESPANYA_20DHA_ME:
        case TIPO_TARIFA_ELECTRICA_ESPANYA_21DHA_P:
        case TIPO_TARIFA_ELECTRICA_ESPANYA_21DHA_B:
        case TIPO_TARIFA_ELECTRICA_ESPANYA_21DHA_C:
        case TIPO_TARIFA_ELECTRICA_ESPANYA_21DHA_CE:
        case TIPO_TARIFA_ELECTRICA_ESPANYA_21DHA_ME: {
            caracteristicas_tipo_tarifa["numero_tramos"] = 2;
            caracteristicas_tipo_tarifa["tramos_potencias_iguales"] = null;
            caracteristicas_tipo_tarifa["tipo_calculo_coste_potencias"] = TIPO_CALCULO_COSTE_POTENCIAS_SIN_EXCESOS;
            caracteristicas_tipo_tarifa["excesos_energia_reactiva"] = false;
            caracteristicas_tipo_tarifa["tramos_penalizables_energia_reactiva"] = null;
            caracteristicas_tipo_tarifa["parametros_medida_datos_facturacion"] = false;
            caracteristicas_tipo_tarifa["potencia_maxima_tramos"] = POTENCIA_MAXIMA_TARIFA_ELECTRICA_ESPANYA_2_TRAMOS;
            caracteristicas_tipo_tarifa["potencias_tramos_crecientes"] = false;
            caracteristicas_tipo_tarifa["potencia_minima_algun_tramo"] = null;
            break;
        }
        case TIPO_TARIFA_ELECTRICA_ESPANYA_20DHA_P_MAXIMETRO:
        case TIPO_TARIFA_ELECTRICA_ESPANYA_20DHA_B_MAXIMETRO:
        case TIPO_TARIFA_ELECTRICA_ESPANYA_20DHA_C_MAXIMETRO:
        case TIPO_TARIFA_ELECTRICA_ESPANYA_20DHA_CE_MAXIMETRO:
        case TIPO_TARIFA_ELECTRICA_ESPANYA_20DHA_ME_MAXIMETRO:
        case TIPO_TARIFA_ELECTRICA_ESPANYA_21DHA_P_MAXIMETRO:
        case TIPO_TARIFA_ELECTRICA_ESPANYA_21DHA_B_MAXIMETRO:
        case TIPO_TARIFA_ELECTRICA_ESPANYA_21DHA_C_MAXIMETRO:
        case TIPO_TARIFA_ELECTRICA_ESPANYA_21DHA_CE_MAXIMETRO:
        case TIPO_TARIFA_ELECTRICA_ESPANYA_21DHA_ME_MAXIMETRO: {
            caracteristicas_tipo_tarifa["numero_tramos"] = 2;
            caracteristicas_tipo_tarifa["tramos_potencias_iguales"] = null;
            caracteristicas_tipo_tarifa["tipo_calculo_coste_potencias"] = TIPO_CALCULO_COSTE_POTENCIAS_EXCESOS_MAXIMOS_MENSUALES;
            caracteristicas_tipo_tarifa["excesos_energia_reactiva"] = false;
            caracteristicas_tipo_tarifa["tramos_penalizables_energia_reactiva"] = null;
            caracteristicas_tipo_tarifa["parametros_medida_datos_facturacion"] = false;
            caracteristicas_tipo_tarifa["potencia_maxima_tramos"] = POTENCIA_MAXIMA_TARIFA_ELECTRICA_ESPANYA_2_TRAMOS;
            caracteristicas_tipo_tarifa["potencias_tramos_crecientes"] = false;
            caracteristicas_tipo_tarifa["potencia_minima_algun_tramo"] = null;
            break;
        }
        case TIPO_TARIFA_ELECTRICA_ESPANYA_30A_P:
        case TIPO_TARIFA_ELECTRICA_ESPANYA_30A_B:
        case TIPO_TARIFA_ELECTRICA_ESPANYA_30A_C:
        case TIPO_TARIFA_ELECTRICA_ESPANYA_30A_CE:
        case TIPO_TARIFA_ELECTRICA_ESPANYA_30A_ME: {
            caracteristicas_tipo_tarifa["numero_tramos"] = 3;
            caracteristicas_tipo_tarifa["tramos_potencias_iguales"] = null;
            caracteristicas_tipo_tarifa["tipo_calculo_coste_potencias"] = TIPO_CALCULO_COSTE_POTENCIAS_EXCESOS_MAXIMOS_MENSUALES;
            caracteristicas_tipo_tarifa["excesos_energia_reactiva"] = true;
            caracteristicas_tipo_tarifa["tramos_penalizables_energia_reactiva"] = [1, 2];
            caracteristicas_tipo_tarifa["parametros_medida_datos_facturacion"] = false;
            caracteristicas_tipo_tarifa["potencia_maxima_tramos"] = null;
            caracteristicas_tipo_tarifa["potencias_tramos_crecientes"] = false;
            caracteristicas_tipo_tarifa["potencia_minima_algun_tramo"] = POTENCIA_MINIMA_TARIFA_ELECTRICA_ESPANYA_3_TRAMOS_0_ALGUN_TRAMO + INCREMENTO_POTENCIA_MINIMA_ALGUN_TRAMO;
            break;
        }
        case TIPO_TARIFA_ELECTRICA_ESPANYA_31A_P:
        case TIPO_TARIFA_ELECTRICA_ESPANYA_31A_B:
        case TIPO_TARIFA_ELECTRICA_ESPANYA_31A_C:
        case TIPO_TARIFA_ELECTRICA_ESPANYA_31A_CE:
        case TIPO_TARIFA_ELECTRICA_ESPANYA_31A_ME: {
            caracteristicas_tipo_tarifa["numero_tramos"] = 3;
            caracteristicas_tipo_tarifa["tramos_potencias_iguales"] = null;
            caracteristicas_tipo_tarifa["tipo_calculo_coste_potencias"] = TIPO_CALCULO_COSTE_POTENCIAS_EXCESOS_MAXIMOS_MENSUALES;
            caracteristicas_tipo_tarifa["excesos_energia_reactiva"] = true;
            caracteristicas_tipo_tarifa["tramos_penalizables_energia_reactiva"] = [1, 2];
            caracteristicas_tipo_tarifa["parametros_medida_datos_facturacion"] = true;
            caracteristicas_tipo_tarifa["potencia_maxima_tramos"] = POTENCIA_MAXIMA_TARIFA_ELECTRICA_ESPANYA_3_TRAMOS_1;
            caracteristicas_tipo_tarifa["potencias_tramos_crecientes"] = true;
            caracteristicas_tipo_tarifa["potencia_minima_algun_tramo"] = null;
            break;
        }
        case TIPO_TARIFA_ELECTRICA_ESPANYA_61A_P:
        case TIPO_TARIFA_ELECTRICA_ESPANYA_61A_B:
        case TIPO_TARIFA_ELECTRICA_ESPANYA_61A_C:
        case TIPO_TARIFA_ELECTRICA_ESPANYA_61A_CE:
        case TIPO_TARIFA_ELECTRICA_ESPANYA_61A_ME: {
            caracteristicas_tipo_tarifa["numero_tramos"] = 6;
            caracteristicas_tipo_tarifa["tramos_potencias_iguales"] = null;
            caracteristicas_tipo_tarifa["tipo_calculo_coste_potencias"] = TIPO_CALCULO_COSTE_POTENCIAS_EXCESOS_CUARTOHORARIOS;
            caracteristicas_tipo_tarifa["excesos_energia_reactiva"] = true;
            caracteristicas_tipo_tarifa["tramos_penalizables_energia_reactiva"] = [1, 2, 3, 4, 5];
            caracteristicas_tipo_tarifa["parametros_medida_datos_facturacion"] = false;
            caracteristicas_tipo_tarifa["potencia_maxima_tramos"] = null;
            caracteristicas_tipo_tarifa["potencias_tramos_crecientes"] = true;
            caracteristicas_tipo_tarifa["potencia_minima_algun_tramo"] = POTENCIA_MINIMA_TARIFA_ELECTRICA_ESPANYA_6_TRAMOS_1_ALGUN_TRAMO;
            break;
        }
        case TIPO_TARIFA_ELECTRICA_ESPANYA_62A_P:
        case TIPO_TARIFA_ELECTRICA_ESPANYA_62A_B:
        case TIPO_TARIFA_ELECTRICA_ESPANYA_62A_C:
        case TIPO_TARIFA_ELECTRICA_ESPANYA_62A_CE:
        case TIPO_TARIFA_ELECTRICA_ESPANYA_62A_ME:
        case TIPO_TARIFA_ELECTRICA_ESPANYA_63A_P:
        case TIPO_TARIFA_ELECTRICA_ESPANYA_63A_B:
        case TIPO_TARIFA_ELECTRICA_ESPANYA_63A_C:
        case TIPO_TARIFA_ELECTRICA_ESPANYA_63A_CE:
        case TIPO_TARIFA_ELECTRICA_ESPANYA_63A_ME:
        case TIPO_TARIFA_ELECTRICA_ESPANYA_64A_P:
        case TIPO_TARIFA_ELECTRICA_ESPANYA_64A_B:
        case TIPO_TARIFA_ELECTRICA_ESPANYA_64A_C:
        case TIPO_TARIFA_ELECTRICA_ESPANYA_64A_CE:
        case TIPO_TARIFA_ELECTRICA_ESPANYA_64A_ME: {
            caracteristicas_tipo_tarifa["numero_tramos"] = 6;
            caracteristicas_tipo_tarifa["tramos_potencias_iguales"] = null;
            caracteristicas_tipo_tarifa["tipo_calculo_coste_potencias"] = TIPO_CALCULO_COSTE_POTENCIAS_EXCESOS_CUARTOHORARIOS;
            caracteristicas_tipo_tarifa["excesos_energia_reactiva"] = true;
            caracteristicas_tipo_tarifa["tramos_penalizables_energia_reactiva"] = [1, 2, 3, 4, 5];
            caracteristicas_tipo_tarifa["parametros_medida_datos_facturacion"] = false;
            caracteristicas_tipo_tarifa["potencia_maxima_tramos"] = null;
            caracteristicas_tipo_tarifa["potencias_tramos_crecientes"] = true;
            caracteristicas_tipo_tarifa["potencia_minima_algun_tramo"] = null;
            break;
        }
        case TIPO_TARIFA_ELECTRICA_VENTA_ENERGIA:{
            caracteristicas_tipo_tarifa["numero_tramos"] = 1;
            caracteristicas_tipo_tarifa["tramos_potencias_iguales"] = null;
            caracteristicas_tipo_tarifa["tipo_calculo_coste_potencias"] = TIPO_CALCULO_COSTE_POTENCIAS_SIN_EXCESOS;
            caracteristicas_tipo_tarifa["excesos_energia_reactiva"] = false;
            caracteristicas_tipo_tarifa["tramos_penalizables_energia_reactiva"] = null;
            caracteristicas_tipo_tarifa["parametros_medida_datos_facturacion"] = false;
            caracteristicas_tipo_tarifa["potencia_maxima_tramos"] = null;
            caracteristicas_tipo_tarifa["potencias_tramos_crecientes"] = false;
            caracteristicas_tipo_tarifa["potencia_minima_algun_tramo"] = null;
            break;
        }
        // Ninguno y todos
        case TIPO_TARIFA_NINGUNO:
        case TIPO_TARIFA_TODOS: {
            caracteristicas_tipo_tarifa["numero_tramos"] = 0;
            caracteristicas_tipo_tarifa["tramos_potencias_iguales"] = null;
            caracteristicas_tipo_tarifa["tipo_calculo_coste_potencias"] = TIPO_CALCULO_COSTE_POTENCIAS_NINGUNO;
            caracteristicas_tipo_tarifa["excesos_energia_reactiva"] = false;
            caracteristicas_tipo_tarifa["tramos_penalizables_energia_reactiva"] = null;
            caracteristicas_tipo_tarifa["parametros_medida_datos_facturacion"] = false;
            caracteristicas_tipo_tarifa["potencia_maxima_tramos"] = null;
            caracteristicas_tipo_tarifa["potencias_tramos_crecientes"] = false;
            caracteristicas_tipo_tarifa["potencia_minima_algun_tramo"] = null;
            break;
        }
    }

    // Tipo de tarifa de canarias
    var tipo_tarifa_canarias = null;
    switch (tipo_tarifa)
    {
        case TIPO_TARIFA_ELECTRICA_ESPANYA_20TD_C:
        case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_C:
        case TIPO_TARIFA_ELECTRICA_ESPANYA_30TD_C_MAX:
        case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_C:
        case TIPO_TARIFA_ELECTRICA_ESPANYA_61TD_C_MAX:
        case TIPO_TARIFA_ELECTRICA_ESPANYA_62TD_C:
        case TIPO_TARIFA_ELECTRICA_ESPANYA_63TD_C:
        case TIPO_TARIFA_ELECTRICA_ESPANYA_64TD_C:
        case TIPO_TARIFA_ELECTRICA_ESPANYA_20DHA_C:
        case TIPO_TARIFA_ELECTRICA_ESPANYA_20DHA_C_MAXIMETRO:
        case TIPO_TARIFA_ELECTRICA_ESPANYA_21DHA_C:
        case TIPO_TARIFA_ELECTRICA_ESPANYA_21DHA_C_MAXIMETRO:
        case TIPO_TARIFA_ELECTRICA_ESPANYA_30A_C:
        case TIPO_TARIFA_ELECTRICA_ESPANYA_31A_C:
        case TIPO_TARIFA_ELECTRICA_ESPANYA_61A_C:
        case TIPO_TARIFA_ELECTRICA_ESPANYA_62A_C:
        case TIPO_TARIFA_ELECTRICA_ESPANYA_63A_C:
        case TIPO_TARIFA_ELECTRICA_ESPANYA_64A_C: {
            tipo_tarifa_canarias = true;
            break;
        }
        default: {
            tipo_tarifa_canarias = false;
            break;
        }
    }
    caracteristicas_tipo_tarifa["tipo_tarifa_canarias"] = tipo_tarifa_canarias;

    // Se devuelven las características del tipo de tarifa
    return (caracteristicas_tipo_tarifa);
}
