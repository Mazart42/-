/* tapka — standalone version (no PHP/MySQL required) */
const $ = (sel, root = document) => root.querySelector(sel);
const $$ = (sel, root = document) => [...root.querySelectorAll(sel)];

const CITIES = [
  'Новосибирск','Кемерово','Топки','Улан-Удэ',
  'Ордынка','Барабинск','Тогучин','Душанбе (Таджикистан)'
];

const CARS = [
  // ── Новосибирск ──────────────────────────────────────────────────────────────
  { id:1,  brand:'Hyundai',       model:'Solaris Active',    car_class:'economy',  year:2022, transmission:'auto',   fuel:'petrol',   seats:5, price_day:2600,  deposit:8000,  mileage_limit:300, city:'Новосибирск',           description:'Экономичный седан для города: небольшой расход, камера заднего вида и кондиционер.',          image:'assets/cars/solaris.svg' },
  { id:8,  brand:'BMW',           model:'520i G30',          car_class:'business', year:2021, transmission:'auto',   fuel:'petrol',   seats:5, price_day:7800,  deposit:28000, mileage_limit:350, city:'Новосибирск',           description:'Динамичный бизнес-седан для встреч, трассы и представительских поездок.',                     image:'assets/cars/camry.svg' },
  { id:15, brand:'Zeekr',         model:'001',               car_class:'electric', year:2024, transmission:'auto',   fuel:'electric', seats:5, price_day:11800, deposit:40000, mileage_limit:350, city:'Новосибирск',           description:'Электро-грантурер с полным приводом, просторным салоном и премиальной динамикой.',             image:'assets/cars/tesla.svg' },
  { id:20, brand:'Audi',          model:'A6 Quattro',        car_class:'premium',  year:2023, transmission:'auto',   fuel:'petrol',   seats:5, price_day:11200, deposit:38000, mileage_limit:350, city:'Новосибирск',           description:'Полноприводный премиум-седан для статуса, комфорта и уверенной динамики.',                    image:'assets/cars/camry.svg' },

  // ── Кемерово ─────────────────────────────────────────────────────────────────
  { id:4,  brand:'Kia',           model:'K5 Prestige',       car_class:'comfort',  year:2023, transmission:'auto',   fuel:'petrol',   seats:5, price_day:4200,  deposit:12000, mileage_limit:350, city:'Кемерово',              description:'Комфортный седан для деловых поездок, трассы и ежедневных маршрутов.',                        image:'assets/cars/k5.svg' },
  { id:18, brand:'Mercedes-Benz', model:'Vito Tourer',       car_class:'minivan',  year:2022, transmission:'auto',   fuel:'diesel',   seats:8, price_day:9800,  deposit:30000, mileage_limit:450, city:'Кемерово',              description:'Комфортный пассажирский минивэн для корпоративных поездок и семьи.',                          image:'assets/cars/caravelle.svg' },

  // ── Топки ────────────────────────────────────────────────────────────────────
  { id:2,  brand:'Lada',          model:'Vesta SW Cross',    car_class:'economy',  year:2023, transmission:'manual', fuel:'petrol',   seats:5, price_day:2400,  deposit:7000,  mileage_limit:300, city:'Топки',                 description:'Практичный универсал с высоким клиренсом для ежедневных поездок и багажа.',                   image:'assets/cars/solaris.svg' },
  { id:7,  brand:'Toyota',        model:'Camry 70',          car_class:'business', year:2022, transmission:'auto',   fuel:'petrol',   seats:5, price_day:5900,  deposit:18000, mileage_limit:350, city:'Топки',                 description:'Бизнес-класс с просторным салоном, мягкой подвеской и премиальной акустикой.',                image:'assets/cars/camry.svg' },

  // ── Улан-Удэ (премиум и бизнес) ──────────────────────────────────────────────
  { id:10, brand:'Geely',         model:'Monjaro Flagship',  car_class:'suv',      year:2024, transmission:'auto',   fuel:'petrol',   seats:5, price_day:7200,  deposit:22000, mileage_limit:400, city:'Улан-Удэ',              description:'Полноприводный кроссовер для поездок по городу, трассе и загородным маршрутам.',              image:'assets/cars/monjaro.svg' },
  { id:17, brand:'Hyundai',       model:'Staria',            car_class:'minivan',  year:2023, transmission:'auto',   fuel:'diesel',   seats:8, price_day:9300,  deposit:26000, mileage_limit:450, city:'Улан-Удэ',              description:'Современный минивэн для трансфера, туристической группы и дальних поездок.',                  image:'assets/cars/caravelle.svg' },
  { id:21, brand:'BMW',           model:'7 Series',          car_class:'premium',  year:2023, transmission:'auto',   fuel:'petrol',   seats:5, price_day:14500, deposit:55000, mileage_limit:350, city:'Улан-Удэ',              description:'Флагманский седан BMW: массажные кресла, панорамная крыша, полный привод xDrive.',             image:'assets/cars/camry.svg' },
  { id:22, brand:'Mercedes-Benz', model:'S 500',             car_class:'premium',  year:2022, transmission:'auto',   fuel:'petrol',   seats:5, price_day:17800, deposit:70000, mileage_limit:300, city:'Улан-Удэ',              description:'Вершина представительского класса: воздушная подвеска, салон-люкс и тишина на трассе.',       image:'assets/cars/camry.svg' },
  { id:23, brand:'Lexus',         model:'LX 600',            car_class:'premium',  year:2024, transmission:'auto',   fuel:'petrol',   seats:7, price_day:16200, deposit:60000, mileage_limit:400, city:'Улан-Удэ',              description:'Премиальный внедорожник для дальних маршрутов и сложных дорог Бурятии.',                      image:'assets/cars/monjaro.svg' },
  { id:24, brand:'Toyota',        model:'Crown',             car_class:'business', year:2023, transmission:'auto',   fuel:'hybrid',   seats:5, price_day:8900,  deposit:32000, mileage_limit:350, city:'Улан-Удэ',              description:'Гибридный бизнес-седан нового поколения: стиль, тишина и экономия топлива.',                  image:'assets/cars/camry.svg' },
  { id:25, brand:'Mercedes-Benz', model:'E 300',             car_class:'business', year:2023, transmission:'auto',   fuel:'petrol',   seats:5, price_day:9600,  deposit:34000, mileage_limit:350, city:'Улан-Удэ',              description:'Обновлённый E-класс с подогревом руля, проекционным дисплеем и системой ассистентов.',        image:'assets/cars/camry.svg' },
  { id:26, brand:'Audi',          model:'A8 L',              car_class:'premium',  year:2022, transmission:'auto',   fuel:'petrol',   seats:5, price_day:15500, deposit:58000, mileage_limit:300, city:'Улан-Удэ',              description:'Удлинённый премиум-седан Audi с quattro, мягкой пневмоподвеской и цифровой приборкой.',        image:'assets/cars/camry.svg' },

  // ── Ордынка (только эконом) ───────────────────────────────────────────────────
  { id:6,  brand:'Toyota',        model:'Corolla',           car_class:'economy',  year:2021, transmission:'auto',   fuel:'petrol',   seats:5, price_day:2900,  deposit:8500,  mileage_limit:320, city:'Ордынка',               description:'Надёжный и экономичный седан для поездок по НСО и коротких трасс.',                           image:'assets/cars/solaris.svg' },
  { id:27, brand:'Renault',       model:'Duster',            car_class:'economy',  year:2022, transmission:'auto',   fuel:'petrol',   seats:5, price_day:2700,  deposit:7500,  mileage_limit:300, city:'Ордынка',               description:'Бюджетный кроссовер с высоким клиренсом — удобен для загородных выездов из Ордынки.',         image:'assets/cars/solaris.svg' },
  { id:28, brand:'Hyundai',       model:'Solaris',           car_class:'economy',  year:2023, transmission:'manual', fuel:'petrol',   seats:5, price_day:2500,  deposit:7000,  mileage_limit:300, city:'Ордынка',               description:'Простой в управлении седан для ежедневных нужд и коротких командировок.',                     image:'assets/cars/solaris.svg' },

  // ── Барабинск (только эконом) ─────────────────────────────────────────────────
  { id:29, brand:'Lada',          model:'Granta',            car_class:'economy',  year:2023, transmission:'auto',   fuel:'petrol',   seats:5, price_day:2100,  deposit:6000,  mileage_limit:280, city:'Барабинск',             description:'Доступный и неприхотливый автомобиль для командировок и трассовых поездок.',                  image:'assets/cars/solaris.svg' },
  { id:30, brand:'Kia',           model:'Rio',               car_class:'economy',  year:2022, transmission:'auto',   fuel:'petrol',   seats:5, price_day:2350,  deposit:6500,  mileage_limit:290, city:'Барабинск',             description:'Компактный и экономичный седан — оптимальный вариант для трассы Новосибирск–Омск.',            image:'assets/cars/solaris.svg' },
  { id:31, brand:'Renault',       model:'Logan',             car_class:'economy',  year:2021, transmission:'manual', fuel:'petrol',   seats:5, price_day:2000,  deposit:5500,  mileage_limit:280, city:'Барабинск',             description:'Простой и надёжный седан для бюджетной аренды в Барабинске.',                                  image:'assets/cars/solaris.svg' },

  // ── Тогучин ───────────────────────────────────────────────────────────────────
  { id:3,  brand:'Renault',       model:'Logan Stepway',     car_class:'economy',  year:2021, transmission:'auto',   fuel:'petrol',   seats:5, price_day:2300,  deposit:6500,  mileage_limit:280, city:'Тогучин',               description:'Простой и надёжный автомобиль для недорогой аренды на каждый день.',                           image:'assets/cars/solaris.svg' },
  { id:11, brand:'Haval',         model:'Jolion',            car_class:'suv',      year:2023, transmission:'robot',  fuel:'petrol',   seats:5, price_day:4800,  deposit:14000, mileage_limit:350, city:'Тогучин',               description:'Универсальный кроссовер с высоким клиренсом и удобной мультимедиа.',                          image:'assets/cars/monjaro.svg' },

  // ── Душанбе ───────────────────────────────────────────────────────────────────
  { id:9,  brand:'Mercedes-Benz', model:'E 200',             car_class:'business', year:2022, transmission:'auto',   fuel:'petrol',   seats:5, price_day:8400,  deposit:30000, mileage_limit:350, city:'Душанбе (Таджикистан)', description:'Статусный седан с тихим салоном, комфортными креслами и аккуратной подачей.',                image:'assets/cars/camry.svg' },
  { id:14, brand:'BYD',           model:'Dolphin',           car_class:'electric', year:2024, transmission:'auto',   fuel:'electric', seats:5, price_day:6900,  deposit:18000, mileage_limit:320, city:'Душанбе (Таджикистан)', description:'Компактный электромобиль для спокойных городских поездок и экономичной аренды.',             image:'assets/cars/tesla.svg' },
  { id:19, brand:'Lexus',         model:'ES 250',            car_class:'premium',  year:2022, transmission:'auto',   fuel:'petrol',   seats:5, price_day:9900,  deposit:35000, mileage_limit:350, city:'Душанбе (Таджикистан)', description:'Премиальный седан с мягкой подвеской, кожаным салоном и высоким уровнем тишины.',             image:'assets/cars/camry.svg' },
];

const classNames = { economy:'Эконом', comfort:'Комфорт', business:'Бизнес', suv:'SUV', electric:'Электро', minivan:'Минивэн', premium:'Премиум' };
const fuelNames  = { petrol:'Бензин', diesel:'Дизель', hybrid:'Гибрид', electric:'Электро' };
const transNames = { auto:'Автомат', manual:'Механика', robot:'Робот' };

const state = { cars:[], user:null, filters:{ class:'all', city:'all', q:'' }, selectedCar:null };

function esc(v){ return String(v??'').replace(/[&<>'"]/g,c=>({'&':'&amp;','<':'&lt;','>':'&gt;',"'":"&#39;",'"':'&quot;'}[c])); }
function money(v){ return Number(v||0).toLocaleString('ru-RU')+' ₽'; }
function todayISO(offset=0){ const d=new Date(); d.setDate(d.getDate()+offset); return d.toISOString().slice(0,10); }
function diffDays(a,b){ const n=Math.round((new Date(b)-new Date(a))/86400000); return Number.isFinite(n)&&n>0?n:1; }

function toast(msg, type='ok'){
  const box=$('#toast'); if(!box)return;
  box.textContent=msg; box.dataset.type=type; box.classList.add('show');
  clearTimeout(box._t); box._t=setTimeout(()=>box.classList.remove('show'),3600);
}

/* ---------- Фильтрация ---------- */
function filterCars(){
  const {class:cls,city,q}=state.filters, lq=q.toLowerCase();
  state.cars=CARS.filter(c=>{
    if(c.status&&c.status!=='available')return false;
    if(cls!=='all'&&c.car_class!==cls)return false;
    if(city!=='all'&&c.city!==city)return false;
    if(lq&&!`${c.brand} ${c.model} ${c.description}`.toLowerCase().includes(lq))return false;
    return true;
  }).sort((a,b)=>a.price_day-b.price_day);
}

/* ---------- Рендер авто ---------- */
function renderCars(){
  const grid=$('#carGrid'), empty=$('#emptyState'); if(!grid)return;
  filterCars();
  empty.hidden=state.cars.length>0;
  grid.innerHTML=state.cars.map(car=>`
    <article class="car-card">
      <div class="car-image">
        <div class="car-image-label"><strong>tapka</strong> <span>${esc(car.brand)} ${esc(car.model)}</span></div>
        <img src="${esc(car.image)}" alt="${esc(car.brand+' '+car.model)}" loading="lazy">
      </div>
      <div class="car-content">
        <div class="car-topline"><span class="badge">${classNames[car.car_class]||car.car_class}</span><span>${esc(car.city)}</span></div>
        <h3>${esc(car.brand)} ${esc(car.model)}</h3>
        <p>${esc(car.description)}</p>
        <div class="car-specs">
          <span>${esc(car.year)} год</span>
          <span>${transNames[car.transmission]||car.transmission}</span>
          <span>${fuelNames[car.fuel]||car.fuel}</span>
          <span>${esc(car.seats)} мест</span>
        </div>
        <div class="car-bottom">
          <div><span class="muted">сутки</span><strong>${money(car.price_day)}</strong></div>
          <button class="btn small" type="button" data-book-car="${car.id}">Забронировать</button>
        </div>
      </div>
    </article>`).join('');
}

/* ---------- Города ---------- */
function populateCities(){
  const f=$('#cityFilter');
  if(f){
    const cur=f.value||'all';
    f.innerHTML='<option value="all">Все города</option>';
    CITIES.forEach(c=>{ const o=document.createElement('option'); o.value=c; o.textContent=c; f.appendChild(o); });
    f.value=CITIES.includes(cur)?cur:'all';
  }
  $$('[data-city-select]').forEach(sel=>{
    const cur=sel.value;
    sel.innerHTML='<option value="">Город выдачи</option>';
    CITIES.forEach(c=>{ const o=document.createElement('option'); o.value=c; o.textContent=c; sel.appendChild(o); });
    if(CITIES.includes(cur))sel.value=cur;
  });
}

/* ---------- Фильтры ---------- */
function setActiveClass(v){
  state.filters.class=v;
  $$('.chip').forEach(c=>c.classList.toggle('active',c.dataset.class===v));
  const s=$('#classFilter'); if(s)s.value=v;
}

function initFilters(){
  $$('.chip').forEach(c=>c.addEventListener('click',()=>{ setActiveClass(c.dataset.class||'all'); renderCars(); }));
  $('#classFilter')?.addEventListener('change',e=>{ setActiveClass(e.target.value); renderCars(); });
  $('#cityFilter')?.addEventListener('change',e=>{ state.filters.city=e.target.value; renderCars(); });

  let t;
  $('#searchInput')?.addEventListener('input',e=>{
    clearTimeout(t); t=setTimeout(()=>{ state.filters.q=e.target.value.trim(); renderCars(); },200);
  });

  $('#quickSearch')?.addEventListener('submit',e=>{
    e.preventDefault();
    state.filters.city=$('#cityFilter').value;
    setActiveClass($('#classFilter').value);
    $('#fleet')?.scrollIntoView({behavior:'smooth'});
    renderCars();
  });

  $$('[data-city-pick]').forEach(card=>card.addEventListener('click',()=>{
    const city=card.dataset.cityPick;
    $$('.city-card').forEach(c=>c.classList.remove('city-card--active'));
    card.classList.add('city-card--active');
    const f=$('#cityFilter'); if(f)f.value=city;
    state.filters.city=city;
    $('#fleet')?.scrollIntoView({behavior:'smooth'});
    renderCars();
  }));
}

/* ---------- Даты ---------- */
function initDates(){
  const from=$('#quickFrom'), to=$('#quickTo');
  if(from&&to){ from.min=todayISO(); to.min=todayISO(1); from.value=todayISO(1); to.value=todayISO(3); }
  $$('input[type="date"]').forEach(i=>{ i.min=i.name==='date_to'?todayISO(1):todayISO(); });
}

/* ---------- Бронирование ---------- */
function updateTotal(){
  const form=$('#bookingForm'), total=$('#bookingTotal');
  if(!form||!total||!state.selectedCar)return;
  const days=diffDays(form.date_from.value,form.date_to.value);
  total.textContent=`${money(days*Number(state.selectedCar.price_day))} · ${days} сут.`;
}

function fillFromUser(form){
  if(!state.user||!form)return;
  if(state.user.name)  form.customer_name.value=state.user.name;
  if(state.user.email) form.customer_email.value=state.user.email;
  if(state.user.phone) form.customer_phone.value=state.user.phone;
}

function openBooking(carId){
  const car=CARS.find(c=>Number(c.id)===Number(carId));
  if(!car){ toast('Выберите автомобиль из каталога','error'); return; }
  state.selectedCar=car;
  const modal=$('#bookingModal'), summary=$('#bookingSummary'), form=$('#bookingForm');
  const qFrom=$('#quickFrom')?.value||todayISO(1);
  const qTo=$('#quickTo')?.value||todayISO(3);

  summary.innerHTML=`
    <img src="${esc(car.image)}" alt="${esc(car.brand+' '+car.model)}">
    <div class="badge">${classNames[car.car_class]||car.car_class}</div>
    <h2>${esc(car.brand)} ${esc(car.model)}</h2>
    <p>${esc(car.description)}</p>
    <dl>
      <div><dt>Цена</dt><dd>${money(car.price_day)} / сутки</dd></div>
      <div><dt>Залог</dt><dd>${money(car.deposit)}</dd></div>
      <div><dt>Пробег</dt><dd>${esc(car.mileage_limit)} км/сутки</dd></div>
    </dl>`;

  form.reset();
  populateCities();
  form.car_id.value=car.id;
  form.pickup_city.value=car.city;
  form.date_from.value=qFrom;
  form.date_to.value=qTo>qFrom?qTo:todayISO(3);
  fillFromUser(form);
  updateTotal();
  modal.showModal();
}

function initBooking(){
  document.addEventListener('click',e=>{
    const btn=e.target.closest('[data-book-car]');
    if(btn){ openBooking(btn.dataset.bookCar); return; }
    if(e.target.closest('[data-open-booking]')){ openBooking(state.cars[0]?.id); return; }
  });
  $('#bookingForm')?.addEventListener('input',e=>{ if(e.target.matches('input[type="date"]'))updateTotal(); });
  $('#bookingForm')?.addEventListener('submit',e=>{
    e.preventDefault();
    const data=Object.fromEntries(new FormData(e.currentTarget).entries());
    data.created_at=new Date().toISOString();
    data.car_name=`${state.selectedCar?.brand} ${state.selectedCar?.model}`;
    data.id=Date.now();
    const bookings=JSON.parse(localStorage.getItem('tapka_bookings')||'[]');
    bookings.push(data);
    localStorage.setItem('tapka_bookings',JSON.stringify(bookings));
    $('#bookingModal').close();
    toast(`Заявка на ${data.car_name} принята! Менеджер свяжется с вами.`);
  });
}

/* ---------- Авторизация (demo) ---------- */
function updateAuthUI(){
  const btn=$('#authButton'); if(!btn)return;
  if(state.user){
    btn.textContent=`${state.user.name} · выйти`;
    btn.onclick=()=>{ localStorage.removeItem('tapka_user'); state.user=null; updateAuthUI(); toast('Вы вышли из аккаунта'); };
  } else {
    btn.textContent='Войти';
    btn.onclick=()=>$('#authModal')?.showModal();
  }
}

function initAuth(){
  $$('.tab[data-auth-tab]').forEach(tab=>tab.addEventListener('click',()=>{
    const mode=tab.dataset.authTab;
    $$('.tab[data-auth-tab]').forEach(t=>t.classList.toggle('active',t===tab));
    $('#loginForm').hidden=mode!=='login';
    $('#registerForm').hidden=mode!=='register';
  }));

  $('#loginForm')?.addEventListener('submit',e=>{
    e.preventDefault();
    const fd=Object.fromEntries(new FormData(e.currentTarget).entries());
    const users=JSON.parse(localStorage.getItem('tapka_users')||'[]');
    const found=users.find(u=>u.email===fd.email&&u.password===fd.password);
    if(!found){ toast('Неверный email или пароль','error'); return; }
    state.user=found; localStorage.setItem('tapka_user',JSON.stringify(found));
    updateAuthUI(); $('#authModal').close(); toast(`Добро пожаловать, ${found.name}!`);
  });

  $('#registerForm')?.addEventListener('submit',e=>{
    e.preventDefault();
    const fd=Object.fromEntries(new FormData(e.currentTarget).entries());
    if((fd.password||'').length<6){ toast('Пароль — минимум 6 символов','error'); return; }
    const users=JSON.parse(localStorage.getItem('tapka_users')||'[]');
    if(users.find(u=>u.email===fd.email)){ toast('Этот email уже зарегистрирован','error'); return; }
    const user={id:Date.now(),name:fd.name,email:fd.email,phone:fd.phone||'',password:fd.password};
    users.push(user); localStorage.setItem('tapka_users',JSON.stringify(users));
    state.user=user; localStorage.setItem('tapka_user',JSON.stringify(user));
    updateAuthUI(); $('#authModal').close(); toast(`Добро пожаловать, ${user.name}!`);
  });

  try { const s=localStorage.getItem('tapka_user'); if(s)state.user=JSON.parse(s); } catch(_){}
}

/* ---------- Контакты ---------- */
function initContact(){
  $('#callbackForm')?.addEventListener('submit',e=>{
    e.preventDefault();
    const data=Object.fromEntries(new FormData(e.currentTarget).entries());
    const list=JSON.parse(localStorage.getItem('tapka_callbacks')||'[]');
    list.push({...data,created_at:new Date().toISOString()});
    localStorage.setItem('tapka_callbacks',JSON.stringify(list));
    e.currentTarget.reset();
    toast('Заявка отправлена! Менеджер позвонит вам в ближайшее время.');
  });
}

/* ---------- Тема ---------- */
function initTheme(){
  if(localStorage.getItem('tapka-theme')==='dark')document.documentElement.classList.add('dark');
  $('#themeToggle')?.addEventListener('click',()=>{
    const dark=document.documentElement.classList.toggle('dark');
    localStorage.setItem('tapka-theme',dark?'dark':'light');
  });
}

/* ---------- Boot ---------- */
function boot(){
  initTheme();
  initDates();
  populateCities();
  initFilters();
  initBooking();
  initAuth();
  initContact();
  updateAuthUI();
  renderCars();
}

boot();
