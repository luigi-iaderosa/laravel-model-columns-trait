

## Project description
This project is intended to explore the possibility of creating a laravel command
that creates a trait cointaining the columns of a given table. 

This should give improved autocompletion possibilities. 


### command example

```
php artisan detect-columns Item Path/Of/Model
```

Second argument is optional, if the model is in the 'App' namespace. 

### progress
|Version|Features|DB
|-------|--------|-----|
|0.1.0| A trait is correctly created under convenient in-app folder in default namespace (App).|Sqlite


###next step 
Review for paths different than standard. 


#links for use

https://medium.com/@devlob/laravel-crud-generators-614caddf8bea

### perspective
Include this logic in a laravel package. 




