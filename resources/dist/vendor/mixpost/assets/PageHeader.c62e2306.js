import{h as a,o as s,d as o,a as i,t as c,i as r,j as l}from"./app.e6e16084.js";const f=()=>({notify:(t,e)=>{a.emit("notify",{variant:t,message:e})}}),d={class:"row-px mb-2xl"},p={class:"flex items-center justify-between"},_={class:"font-bold text-xl"},m={key:0,class:"mt-xs text-lg"},h={__name:"PageHeader",props:{title:{required:!0,type:String}},setup(n){return(t,e)=>(s(),o("div",d,[i("div",p,[i("h1",_,c(t.$props.title),1),r(t.$slots,"default")]),t.$slots.description?(s(),o("div",m,[r(t.$slots,"description")])):l("",!0)]))}};export{h as _,f as u};