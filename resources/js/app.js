import "./bootstrap";
import "flowbite";

import {initFlowbite} from "flowbite";

initFlowbite();
import Alpine from 'alpinejs'


// Livewire.hook('commit', ({ component, commit, respond, succeed, fail }) => {
//     succeed(({ snapshot, effect }) => {
//         queueMicrotask(() => {
//             initFlowbite();
//         })
//     })
// })

document.addEventListener("livewire:navigated", () => {
    initFlowbite();
});

// Alpine.start()
