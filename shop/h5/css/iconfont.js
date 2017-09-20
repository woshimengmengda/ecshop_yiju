(function(window){var svgSprite="<svg>"+""+'<symbol id="icon-qq" viewBox="0 0 1024 1024">'+""+'<path d="M512 64C264 64 64 264 64 512s200 448 448 448 448-200 448-448S760 64 512 64zM748.8 696c-4.8 0-22.4-14.4-30.4-27.2s-19.2-33.6-19.2-33.6l-1.6-1.6C694.4 684.8 672 724.8 640 752c11.2 3.2 24 8 35.2 14.4 19.2 9.6 19.2 19.2 17.6 22.4-1.6 4.8-14.4 9.6-30.4 9.6-8 0-27.2 1.6-49.6 1.6h0c-19.2 0-52.8 0-72-3.2-16-1.6-40-1.6-56 0C465.6 800 430.4 800 411.2 800c-22.4 0-41.6 0-49.6-1.6-17.6-1.6-28.8-4.8-30.4-9.6-1.6-3.2-1.6-14.4 17.6-22.4 11.2-4.8 24-9.6 35.2-14.4-32-27.2-54.4-67.2-59.2-118.4l-1.6 1.6c0 0-9.6 20.8-19.2 33.6s-25.6 27.2-30.4 27.2-6.4-9.6-6.4-25.6c0-14.4 1.6-36.8 19.2-94.4 11.2-40 32-89.6 43.2-112 1.6-179.2 91.2-240 182.4-240s180.8 60.8 182.4 240c11.2 22.4 32 72 43.2 112 16 57.6 17.6 80 19.2 94.4C756.8 684.8 753.6 696 748.8 696z" fill="" ></path>'+""+"</symbol>"+""+'<symbol id="icon-gouwuche" viewBox="0 0 1365 1024">'+""+'<path d="M692.560677 35.401693c-256.301035 0-464.075259 207.774224-464.075258 464.075259s207.774224 464.075259 464.075258 464.075259 464.075259-207.774224 464.075259-464.075259-207.714017-464.075259-464.075259-464.075259zM610.980245 749.57667c-23.601129 0-42.746943-19.085607-42.746943-42.746943 0-23.601129 19.145814-42.746943 42.746943-42.746942s42.746943 19.145814 42.746942 42.746942c0 23.661336-19.145814 42.746943-42.746942 42.746943z m213.855127 0c-23.601129 0-42.746943-19.085607-42.746943-42.746943 0-23.601129 19.085607-42.746943 42.746943-42.746942 23.601129 0 42.80715 19.145814 42.807149 42.746942 0 23.661336-19.145814 42.746943-42.807149 42.746943z m42.807149-114.031985H582.502352l-74.656632-222.70555-42.746943-42.746943h-51.597366c-21.614299 0-23.962371-42.80715 0-42.80715h65.806209l57.015992 57.0762h391.766698l-60.447789 251.183443z"  ></path>'+""+"</symbol>"+""+'<symbol id="icon-dianhua" viewBox="0 0 1024 1024">'+""+'<path d="M515.24 46.53c-246.711 0-446.71 199.998-446.71 446.71 0 246.71 199.999 446.709 446.71 446.709 246.712 0 446.71-199.998 446.71-446.71S761.952 46.53 515.24 46.53z m27.627 275.446c26.034-44.604 45.468-76.555 72.837-120.38 10.43-15.37 22.71-9.538 30.81-4.009 4.714 2.366 17.926 12.408 32.081 22.992L590.14 369.092c-14.827-8.514-26.935-16.1-38.867-23.203-11.868-7.067-11.294-18.966-8.406-23.913z m-225.233 413.99c-14.804-8.45-16.92-18.789-7.617-33.463 16.073-25.356 30.747-51.593 46.292-77.288 28.992-47.923 20.95-57.157 81.847-16.407 0.549 0.366 0.754 1.247 2.428 4.176-28.478 47.805-56.804 95.35-86.114 144.547-13.723-8.047-25.21-14.927-36.836-21.564zM721.62 372.066c-23.929 126.152-86.733 231.627-174.75 322.907-33.379 34.616-73.067 61.059-121.496 69.818-16.521 2.988-34.037 0.474-56.478 0.474 30.924-52.046 57.625-97.318 84.906-142.238 2.118-3.488 8.328-4.456 12.59-6.685 11.527-6.029 27.491-9.146 33.8-18.655 35.246-53.132 68.79-107.438 101.286-162.307 5.969-10.078 10.128-27.847 5.228-36.39-10.815-18.855-2.034-30.768 6.601-44.706 8.72-14.076 17.01-28.42 25.5-42.64 15.61-26.153 31.23-52.301 48.528-81.271 7.245 9.914 12.15 16.586 16.546 23.149 9.434 14.08 20.412 34.665 21.893 58.634 1.118 10.612-1.13 43.97-4.154 59.91z"  ></path>'+""+"</symbol>"+""+"</svg>";var script=function(){var scripts=document.getElementsByTagName("script");return scripts[scripts.length-1]}();var shouldInjectCss=script.getAttribute("data-injectcss");var ready=function(fn){if(document.addEventListener){if(~["complete","loaded","interactive"].indexOf(document.readyState)){setTimeout(fn,0)}else{var loadFn=function(){document.removeEventListener("DOMContentLoaded",loadFn,false);fn()};document.addEventListener("DOMContentLoaded",loadFn,false)}}else if(document.attachEvent){IEContentLoaded(window,fn)}function IEContentLoaded(w,fn){var d=w.document,done=false,init=function(){if(!done){done=true;fn()}};var polling=function(){try{d.documentElement.doScroll("left")}catch(e){setTimeout(polling,50);return}init()};polling();d.onreadystatechange=function(){if(d.readyState=="complete"){d.onreadystatechange=null;init()}}}};var before=function(el,target){target.parentNode.insertBefore(el,target)};var prepend=function(el,target){if(target.firstChild){before(el,target.firstChild)}else{target.appendChild(el)}};function appendSvg(){var div,svg;div=document.createElement("div");div.innerHTML=svgSprite;svgSprite=null;svg=div.getElementsByTagName("svg")[0];if(svg){svg.setAttribute("aria-hidden","true");svg.style.position="absolute";svg.style.width=0;svg.style.height=0;svg.style.overflow="hidden";prepend(svg,document.body)}}if(shouldInjectCss&&!window.__iconfont__svg__cssinject__){window.__iconfont__svg__cssinject__=true;try{document.write("<style>.svgfont {display: inline-block;width: 1em;height: 1em;fill: currentColor;vertical-align: -0.1em;font-size:16px;}</style>")}catch(e){console&&console.log(e)}}ready(appendSvg)})(window)