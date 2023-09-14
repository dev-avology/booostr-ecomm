 "use strict";

 function initialize() {

     var infoWindow = '',
         addressEl = document.querySelector('#location_input'),
         latEl = document.querySelector('#latitude'),
         longEl = document.querySelector('#longitude'),
         city = document.querySelector('#location_city'),
         state = document.querySelector('#location_state'),
         postal = document.querySelector('#post_code');

     /**
      * Creates a search box
      */
     searchBox = new google.maps.places.SearchBox(addressEl);

     /**
      * When the place is changed on search box, it takes the marker to the searched location.
      */
     google.maps.event.addListener(searchBox, 'places_changed', function() {
         var places = searchBox.getPlaces(),
            addresss = places[0].formatted_address;
            let address1 = "";
            let postcode = "";
         console.log(places[0]);
         for (const component of places[0].address_components) {
            // @ts-ignore remove once typings fixed
            const componentType = component.types[0];
            switch (componentType) {
              case "street_number": {
                address1 = `${component.long_name} ${address1}`;
                break;
              }
              case "route": {
                address1 += component.short_name;
                break;
              }
              case "postal_code": {
                postcode = `${component.long_name}${postcode}`;
                break;
              }
        
            //   case "postal_code_suffix": {
            //     postcode = `${postcode}-${component.long_name}`;
            //     break;
            //   }
              case "locality":
                city.value = component.long_name;
                break;
              case "administrative_area_level_1": {
                state.value = component.short_name;
                break;
              }
              case "country":
                //document.querySelector("#country").value = component.long_name;
                break;
            }
          }
          addressEl.value=address1;
          postal.value=postcode;
        
     });

 }