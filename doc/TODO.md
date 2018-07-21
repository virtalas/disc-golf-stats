## Todo list

Features/bugs/etc planned for disc-golf-stats

### Misc

* remember me -functionality (http://fishbowl.pastiche.org/2004/01/19/persistent%5Flogin%5Fcookie%5Fbest%5Fpractice/)
* reset password -functionality
* set collation (order by name not working in page player for the course list, ä before a)
* site icon
* Date-picker http://eonasdan.github.io/bootstrap-datetimepicker/
* testejä tietokannalle, malleille etc. myös testi onko cache päällä
* tuloskortin laskennalle ja esittämiselle oma model player_scores-listan sijaan

### Graphs

* pelien määrä kuukausittain -kaavio: kun painaa 'kaikki pelit', kaavio liikahtaa paikoilleen, korjaa ettei liikahda
* jonkinlainen diagrammi (käytä heatmap, kuten githubissa) pelien lopetusajasta, myös viikonpäivä mukaan(?) (vain sopiva kaavio puuttuu http://raphaeljs.com/github/dots.html http://visible.io/charts/column/punch-card.html)

### Tilastot-sivu

* longest streak (days in row)
* longest birdie streak
* (longest par streak)

### Pelit

* mobile gui: lisää validointi sille että 'sadetta' ja 'märkää_ei_sadetta' ei voi olla samaan aikaan
* mobile gui: tyhjä peli ilmestyy kun valitaan 'vanha lisäyssivu'
* mobile gui: pelaajat eivät mahdu näytölle jos pelaajia on liian monta eikä sivua voi skrollata
* harkintaan: heitot nimen viereen, ob oikeaan laitaan, alusta par:eilla eikä nollilla, +- napit tulokselle
* (pelin lisäys: pelaaja-checkboxit kun pelaajamäärä != 5) (not a defect?)

### Radat-sivu

* pelaaja voi muokata lisäämiään ratoja
    * (muokkaa-painike radan sivulle, ettei tarvitse etsiä sitä)
* korjaa radan lisäysnäkymä jos on monta väylää (ja kapea näyttö), uusi rivi tai jotain
* vaihda popularity <-> name javascriptillä ja päivitä ratalinkkien get-parametri oikeaksi
* rate courses with 5 stars, how overall score and user's own score
* (väylien pituudet) (liikaa ylläpitotyötä?)

### Pelaajat-sivu

* "ratojen tulosten keskiarvot" Kivikon tulokset väärin (skipatut väylät)

### Kisat-sivu

* korjaa kisan muokkaus (ei onnistu ollenkaan)
* päivämäärän formaatti samaksi kuin pelit-sivulla
* pisteet menevät väärin jos jollain laiton peli (ainakin jos laiton + kaikki tulokset nollia)
* kisaa tehdessä ja muokatessa valita "laske pisteet" / "käytä pistelaskua", joka määrää näytetäänkö pistetaulukko
