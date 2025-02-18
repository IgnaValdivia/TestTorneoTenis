# TestTorneoTenis
Simulación del comportamiento de un torneo de tenis
•	La modalidad del torneo es por eliminación directa.
•	Se puede asumir que la cantidad de jugadores es una potencia de 2.
•	El torneo puede ser Femenino o Masculino.
•	Cada jugador tiene un nombre y un nivel de habilidad (valor entre 0 y 100).
•	En un enfrentamiento entre dos jugadores influyen el nivel de habilidad y la suerte para decidir al ganador. La suerte se define como el usuario lo desee en su diseño.
•	En el torneo masculino, se deben considerar la fuerza y la velocidad de desplazamiento como parámetros adicionales para calcular el ganador.
•	En el torneo femenino, se debe considerar el tiempo de reacción como parámetro adicional para calcular el ganador. 
•	No existen empates.
•	Se requiere que, a partir de una lista de jugadores, se simule el torneo y se obtenga como resultado el ganador del mismo.
•	Se recomienda realizar la solución en su IDE preferido.
•	Se valorarán las buenas prácticas de Programación Orientada a Objetos (POO).
•	Se puede definir por parte del usuario cualquier cuestión adicional que considere no aclarada en la consigna.
•	Se pueden agregar las aclaraciones que se consideren necesarias en la entrega del ejercicio.
•	Cualquier extra que aporte será bienvenido.
•	Se prefiere el modelado en capas o el uso de arquitecturas limpias (Clean Architecture).
•	Se prefiere la entrega del código en un sistema de versionado como GitHub, GitLab o Bitbucket.

Nota sobre eliminación directa:
El sistema de eliminación directa implica que el perdedor de cada enfrentamiento es eliminado del torneo, mientras que el ganador avanza a la siguiente fase. El proceso continúa hasta que solo queda un campeón.
Puntos extra (Opcionales)
1.	Testing de la solución (Unit Test).
2.	API REST (Swagger + Integration Testing):
  o	Dado una lista de jugadores, retorna el resultado del torneo.
  o	Permite consultar el resultado de torneos finalizados exitosamente, con algunos filtros como:
    	Torneo Masculino y/o Femenino.
    	Otros criterios a definir.
3.	Uso de una base de datos (en lugar de datos embebidos).
4.	Subir el código a un repositorio en GitHub/GitLab.
5.	Desplegar la solución en un servicio como AWS/Azure, usando Docker o Kubernetes.
