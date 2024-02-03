---
theme: gaia
_class: lead
paginate: true
backgroundColor: #fff
backgroundImage: url('https://marp.app/assets/hero-background.svg')
marp: true
style: |
    .columns {
      display: grid;
      grid-template-columns: repeat(2, minmax(0, 1fr));
      gap: 1rem;
    }
---

![bg left:40% 80%](https://saw21.dibris.unige.it/~S4943369/images/favicon.png)

# **[CornCTF](https://saw21.dibris.unige.it/~S4943369)**

Riccardo Isola
Ludovico Capiaghi

---

# Dominio applicativo

Sul nostro sito proponiamo una game-experience nel formato capture-the-flag con Challenge sulla cybersecurity in cui lo scopo è trovare la flag sfruttando le vulnerabilità dei servizi forniti

Il sito è responsive by design, rispettando le [linee guida di google](https://m3.material.io/), utilizzando la libreria [BeerCSS](https://www.beercss.com/)

---

# Il nostro DB

![w:1150px](er.png)

Per implementare il _remember me_ usiamo JWT

---

# Funzionalità aggiuntive

<div class="columns">
<div>

-   Area amministrativa

![h:200px](admin_list.png)
![h:200px](admin_edit.png)

</div>

<div>

-   Challenge

![h:200px](challenges.png)

</div>
</div>
