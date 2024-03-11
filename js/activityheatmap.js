/* User activity heatmap on user home page and public profile. */

let uid  = document.getElementById("heatmap").getAttribute("data-uid");
const cal = new CalHeatmap();
cal.paint({
    range: 12,
    scale: {
        color: {
            range: ['#ccec2e', '#a6c027'],
            type: 'linear',
            interpolate: 'hsl',
            domain: [0, 15]
        }
    },
    domain: {
        type: 'month',
        gutter: 2
    },
    subDomain: {
        type: "ghDay",
        radius: 2,
        gutter: 2
    },
    date: {
        start: new Date(new Date().setMonth(new Date().getMonth() - 11)),
        locale: { weekStart: 1 },
        highlight: [ new Date() ],
    },
    data: {
        source: '/api/index.php?action=daily_results&fmt=json&u=' + uid,
        type: 'json',
        x: 'date',
        y: d => +d['count'],
    }
},
          [
              [
                  Tooltip,
                  {
                      text: function (date, value, dayjsDate) {
                          return (
                              (value ? value + ' results' : 'No data') + ' on ' + dayjsDate.format('LL')
                          );
                      },
                  },
              ],
          ]
         );
