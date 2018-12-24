ttiPolyfill.getFirstConsistentlyInteractive().then((tti) => {
    const currentPage=document.querySelector("title").text;
    //console.log(`TTI for ${currentPage}: ${tti/1000} s`);
    _performanceMetrics.push({
        hitType: "timing",
        timingCategory: "Load Performance",
        timingLabel:currentPage,
        timingVar: "time-to-interactive",
        timingValue: tti
    });

    if(typeof ga !=="undefined"){
        _performanceMetrics.forEach((metric) =>{
            //console.log(`Time for ${metric.timingVar}  : ${metric.timingValue/1000}s`);
            ga("send", metric); 
        });
    }
    
  });