export function collectUrls(items, urls = [])  {
    let label = '';
    for (const item of items) {
        console.log(item)
        label = item.label
        if (item.url) {
          urls.push(item.url);
        }
        if (item.items) {
          collectUrls(item.items, urls);
        }
      }
      return {
        urls: urls,
        label: label
    };
}

export function hasUrls(items){
  for(const item of items){
    console.log(item);
  }
}

// export function hasUrls(items){
//   for(const item in items){
//     console.log("alksdjfklajdsf: ", item);
//     console.log("ioeqweirouiweo: ", item.label);
//     if(item.items){
//       console.log('item ni: ', item.items);
//       loopHole(item.items);
//     }else{
//       console.log('Not Found')
//     }
//   }
// }

// function loopHole(items: object){
//   for(const item in items){
//     console.log("alksdjfklajdsf: ", item);
//     console.log("ioeqweirouiweo: ", item.label);
//     if(item.items){
//       console.log('item ni: ', item.items);
//       loopHole(item.items);
//     }else{
//       console.log('Not Found')
//     }
//   }
// }