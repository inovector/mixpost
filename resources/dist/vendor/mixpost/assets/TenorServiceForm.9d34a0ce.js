import{r as d,o as _,m as f,w as e,a as s,b as t,f as r,d as p}from"./app.f7b5907c.js";import{u as v}from"./useNotifications.f75a94ae.js";import{a as h,_ as x}from"./PrimaryButton.20a6ca15.js";import{_ as g}from"./Input.1f9c9072.js";import{H as y}from"./HorizontalGroup.cea9d404.js";import{_ as V,a as k}from"./ReadDocHelp.4ddf1b7f.js";const T=t("div",{class:"flex items-center"}," Tenor ",-1),$=t("p",null,"With Tenor you can use GIF's directly in Mixpost.",-1),b=t("p",null,[t("a",{href:"https://console.cloud.google.com/",class:"link",target:"_blank"},"Create an App on Google Console"),r(". ")],-1),C=r("API Key"),N={class:"w-full"},S=r("Save"),H={__name:"TenorServiceForm",props:{form:{required:!0,type:Object}},setup(o){const c=o,{notify:i}=v(),a=d({}),m=()=>{a.value={},p.Inertia.put(route("mixpost.services.update",{service:"tenor"}),c.form,{preserveScroll:!0,onSuccess(){i("success","Tenor credentials have been saved")},onError:l=>{a.value=l}})};return(l,n)=>(_(),f(x,null,{title:e(()=>[T]),description:e(()=>[$,b,s(V,{href:"https://mixpost.app/docs/1.0.0/tenor",class:"mt-xs"})]),default:e(()=>[s(y,{class:"mt-lg"},{title:e(()=>[C]),default:e(()=>[t("div",N,[s(g,{modelValue:o.form.client_id,"onUpdate:modelValue":n[0]||(n[0]=u=>o.form.client_id=u),type:"text",autocomplete:"off"},null,8,["modelValue"]),s(k,{message:a.value.client_id},null,8,["message"])])]),_:1}),s(h,{onClick:m,class:"mt-lg"},{default:e(()=>[S]),_:1})]),_:1}))}};export{H as default};