(window.webpackJsonp=window.webpackJsonp||[]).push([[5],{348:function(e,t,a){"use strict";a.r(t);var r=a(2),o=a(12),n=a(5),c=a(39),l=a(0),i=a.n(l),s=a(6),u=a(23),d=a(111),m=a(27);t.default=()=>{const[e,t]=Object(l.useState)(!1),[a,r]=Object(l.useState)(!1),u=n.a.useStoreState((e=>e.server.data.uuid)),S=n.a.useStoreState((e=>e.status.value)),{clearFlashes:w,clearAndAddHttpError:h}=Object(o.a)(),{connected:O,instance:x}=n.a.useStoreState((e=>e.socket));Object(l.useEffect)((()=>{if(!O||!x||"running"===S)return;const e=e=>{e.toLowerCase().indexOf("you need to agree to the eula in order to run the server")>=0&&t(!0)};return x.addListener(m.a.CONSOLE_OUTPUT,e),()=>{x.removeListener(m.a.CONSOLE_OUTPUT,e)}}),[O,x,S]);return Object(l.useEffect)((()=>{w("feature:eula")}),[]),i.a.createElement(c.b,{visible:e,onDismissed:()=>t(!1),closeOnBackground:!1,showSpinnerOverlay:a},i.a.createElement(p,{key:"feature:eula"}),i.a.createElement(f,null,"Accept Minecraft® EULA"),i.a.createElement(b,null,"Нажимая ",'"Я согласен"'," ниже ты соглашаешься с ",i.a.createElement(g,{target:"_blank",rel:"noreferrer noopener",href:"https://account.mojang.com/documents/minecraft_eula"},"Minecraft® EULA"),"."),i.a.createElement(y,null,i.a.createElement(_,{variant:s.a.Variants.Secondary,onClick:()=>t(!1)},"Отмена"),i.a.createElement(E,{onClick:()=>{r(!0),w("feature:eula"),Object(d.a)(u,"eula.txt","eula=true").then((()=>{"offline"===S&&x&&x.send(m.b.SET_STATE,"restart"),r(!1),t(!1)})).catch((e=>{console.error(e),h({key:"feature:eula",error:e})})).then((()=>r(!1)))}},"Я согласен")))};var p=Object(r.c)(u.a).withConfig({displayName:"EulaModalFeature___StyledFlashMessageRender",componentId:"sc-lrxorp-0"})({marginBottom:"1rem"}),f=Object(r.c)("h2").withConfig({displayName:"EulaModalFeature___StyledH",componentId:"sc-lrxorp-1"})({fontSize:"1.5rem",lineHeight:"2rem",marginBottom:"1rem","--tw-text-opacity":"1",color:"rgba(244, 244, 245, var(--tw-text-opacity))"}),b=Object(r.c)("p").withConfig({displayName:"EulaModalFeature___StyledP",componentId:"sc-lrxorp-2"})({"--tw-text-opacity":"1",color:"rgba(228, 228, 231, var(--tw-text-opacity))"}),g=Object(r.c)("a").withConfig({displayName:"EulaModalFeature___StyledA",componentId:"sc-lrxorp-3"})({"--tw-text-opacity":"1",color:"rgba(96, 165, 250, var(--tw-text-opacity))",textDecoration:"underline",transitionProperty:"background-color, border-color, color, fill, stroke",transitionTimingFunction:"cubic-bezier(0.4, 0, 0.2, 1)",transitionDuration:"150ms"}),y=Object(r.c)("div").withConfig({displayName:"EulaModalFeature___StyledDiv",componentId:"sc-lrxorp-4"})({marginTop:"2rem",display:"flex",alignItems:"center",justifyContent:"flex-end"}),_=Object(r.c)(s.a.Danger).withConfig({displayName:"EulaModalFeature___StyledButtonDanger",componentId:"sc-lrxorp-5"})({width:"auto",borderColor:"rgba(0, 0, 0, 0)"}),E=Object(r.c)(s.a.Success).withConfig({displayName:"EulaModalFeature___StyledButtonSuccess",componentId:"sc-lrxorp-6"})({marginTop:"0px",marginLeft:"1rem",width:"auto"})}}]);