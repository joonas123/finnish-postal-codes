# Finnish postal codes, towns and regions

This is the source code for (http://finnish-postal-codes.ninj.fi). Feel free to use it how you want.

## Resources:

### Regions
Get Finnish regions and their Finnish and Swedish names

Endpoint:

`/api/v1/regions`

Response:
```
[
 {
    "name_fi": "Etelä-Savo",
    "name_se": "Södra Savolax"
 },
 ...
]
```
You can use filter by values, for example:

`/api/v1/regions?name_fi=Etelä-Savo`

Multiple parameter values are also supported:

`/api/v1/regions?name_fi=Etelä-Savo,Pohjanmaa`

### Postal codes
Get Finnish regions and their Finnish and Swedish names

Endpoint:

`/api/v1/postal-codes`

Response:
```
[
 {
    "town_fi": "Oulu",
    "town_se": "Uleåborg",
    "postal_code": "91210",
    "region_name_fi": "Pohjois-Pohjanmaa",
    "region_name_se": "Norra Österbotten"
 },
 ...
]
```

Same as in Regions, you can filter by values:
`/api/v1/postal-codes?town_fi=Oulu,Kempele&region_name_fi=Pohjois-pohjanmaa`

### Get town name for single postal code
This is simple endpoint for specific purposes. Mainly when you want easy way to get town name for specific postal code.

Endpoint:

`/api/v1/town-for-postal-code/{param}`

Response:
```
{
  "town_fi": "Lumijoki",
  "town_se": "Lumijoki"
}
```

### Postal code as key
Get key-value pair list of postal codes where postal code is the key and value is specified column.

Endpoint:
`/api/v1/postal-code-as-key`

Response:
```
{
  "90440": "Kempele",
  "90441": "Kempele",
  "90444": "Kempele",
  ...
}
```
You can define which column you want to get as value by giving the column name as value for `value_column` filter.

Allowed values for `value_column`:
```
town_fi
town_se
postal_code
region_name_fi
region_name_se
```

Same filters apply here than in Regions and Postal Codes endpoints. 
Example:

`/api/v1/postal-code-as-key?value_column=town_se&town_se=Kempele`
