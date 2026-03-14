{
  "function": {
    "api/*.php": {
      "runtime" : "vercel-php@0.9.0"
    }
  },
  "rootes": [
    {
      "src" : "/api/(.*)", 
      "dest" : "/api/$1"
    },
    {
      "src" : "/(.*)", 
      "dest" : "/public/$1"
    }
  ]

}
