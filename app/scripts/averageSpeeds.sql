SELECT date_time_container.date_time,
       SUM((speed_metric.min_speed + speed_metric.max_speed) / 2 * speed_metric.amount_vehicles)
         / SUM(speed_metric.amount_vehicles) AS avg_speed
FROM date_time_container
INNER JOIN speed_metric
 ON date_time_container.id = speed_metric.date_time_container_id
WHERE date_time_container.address_id = 2
GROUP BY date_time_container.id
ORDER BY speed_metric.max_speed,
         date_time_container.date_time
