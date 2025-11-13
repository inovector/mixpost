import{c4 as n}from"./app-CuAAy0_a.js";const j=()=>({notify:(o,t,i)=>{if(typeof t!="object"&&n.emit("notify",{variant:o,message:t,button:i}),typeof t=="object"){const f=Object.keys(t).map(c=>t[c].join(`
`)).join(`
`);n.emit("notify",{variant:o,message:f,button:i})}}});export{j as u};
