# quattro-stagioni

_quattro-stagioni_ is a JavaScript library (pure vanilla ES6) that returns the current season for a given date.
It can also add the current season as CSS class to an HTML DOM element, e.g. to the `body`.
Think _[modernizr][modernizr] for seasons_.

[modernizr]: https://modernizr.com/

## Usage

```bash
npm install --save quattro-stagioni
```

And in you code:

```js
import { QuattroStagioni } from './index.js';

let qs = new QuattroStagioni()
qs.update() // will add a class 'winter', 'spring', 'summer' or 'autumn' to the <body> tag
```

See [src/example.html]() for a full example.

## Options

There are a few options available in this library.

### Change the hemisphere

```js
import { Location, QuattroStagioni } from './index.js';

let locN = Location.NORTHERN_HEMISPHERE
let locS = Location.SOUTHERN_HEMISPHERE

let qsN = new QuattroStagioni(locN)
let qsS = new QuattroStagioni(locS)

console.log(qsN.meteorologicalSeason(), qsS.meteorologicalSeason())
```

### Specific Date

```js
import { QuattroStagioni } from './index.js';

let today = new Date() // change to any date you wish

let qs = new QuattroStagioni()
console.log(qs.meteorologicalSeason(today))
console.log(qs.update(today))
```

## Data Basis

This library works based on the dates reported on [the _Season_ page of Wikipedia][wiki-season], the free encyclopedia.

Currently this library only supports meteorological seasons.

[wiki-season]: https://en.wikipedia.org/w/index.php?title=Season&oldid=947807995#Meteorological

## The Meaning of _Quattro Stagioni_

It's Italian and means _Four Seasons_.
Also, it's the name of a delicious [type of pizza][pizza].

[pizza]: https://en.wikipedia.org/wiki/Pizza_quattro_stagioni

## Development

To work on the `example.html`, run the simple development server:

```
grunt connect
```

Then connect to `http://localhost:8000/example.html`.

## Contributions

Contributions are welcome.
Please open a PR where you describe your contribution.

Make sure that `jshint` does not report any errors.

All contributions have to be made available under the same terms as this project,
i.e. under the terms of the [_Creative Commons Zero_ license][CC0].

## License

[![CC0](http://i.creativecommons.org/p/zero/1.0/88x31.png)][CC0]

This code is in _public domain_.

To the extent possible under law,
the authors have waived all copyright and related or neighboring rights
to _quattro-stagioni_.

[CC0]: http://creativecommons.org/publicdomain/zero/1.0/
