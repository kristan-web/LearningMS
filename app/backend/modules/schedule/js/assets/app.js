import { Calendar } from "@fullcalendar/core";
import dayGridPlugin from "@fullcalendar/daygrid";
import interactionPlugin from "@fullcalendar/interaction";
import "bootstrap/dist/js/bootstrap.bundle.min.js";
import React from "react";
import ReactDOM from "react-dom";
import { Provider, useAlert } from "react-alert";
import alertTemplate from "react-alert-template-basic";

window.FullCalendar = { Calendar };
window.FullCalendarPlugins = { dayGridPlugin, interactionPlugin };

function ServerAlerts() {
  const alert = useAlert();
  React.useEffect(() => {
    document.querySelectorAll(".schedule-alert").forEach((node) => {
      alert.show(node.dataset.alertMessage, { type: node.dataset.alertType });
      node.remove();
    });
  }, [alert]);
  return null;
}

const alertRoot = document.getElementById("react-alert-root");
if (alertRoot) {
  ReactDOM.render(
    React.createElement(
      Provider,
      { template: alertTemplate },
      React.createElement(ServerAlerts),
    ),
    alertRoot,
  );
}
