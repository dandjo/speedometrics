SELECT data_set.date_time,
       SUM((speed_category.range_from + speed_category.range_to) / 2 * speed_category.amount_vehicles)
         / SUM(speed_category.amount_vehicles) AS avg_speed
FROM data_set
INNER JOIN speed_category
 ON data_set.id = speed_category.data_set_id
WHERE data_set.address_id = 2
GROUP BY data_set.id
ORDER BY speed_category.range_to,
         data_set.date_time
